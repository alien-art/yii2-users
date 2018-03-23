<?php

namespace alien\users\models;

use Yii;
use backend\modules\user\components\Helper;
use yii\caching\TagDependency;
use backend\modules\user\components\RouteRule;
use backend\modules\user\components\Configs;
use yii\helpers\VarDumper;
use Exception;

/**
 * Description of Route
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Route extends \yii\base\Object
{
    const CACHE_TAG = 'backend.route';
    const MODULE_TYPE = 1;
    const CONTROLLER_TYPE = 2;
    const ACTION_TYPE = 3;
    
    protected $excluded = ['gii','debug'];

    /**
     * Assign or remove items
     * @param array $routes
     * @return array
     */
    public function addNew($routes, $titles)
    {
        $manager = Yii::$app->getAuthManager();
        $i = 0;
        foreach ($routes as $route) {
            try {
                $r = explode('&', $route);
                $item = $manager->createPermission('/' . trim($route, '/'));
                $item->description = $titles[$i];
                if (count($r) > 1) {
                    $action = '/' . trim($r[0], '/');
                    if (($itemAction = $manager->getPermission($action)) === null) {
                        $itemAction = $manager->createPermission($action);
                        $itemAction->description = $titles[$i];
                        $manager->add($itemAction);
                    }
                    unset($r[0]);
                    foreach ($r as $part) {
                        $part = explode('=', $part);
                        $item->data['params'][$part[0]] = isset($part[1]) ? $part[1] : '';
                    }
                    $this->setDefaultRule();
                    $item->ruleName = RouteRule::RULE_NAME;
                    $manager->add($item);
                    $manager->addChild($item, $itemAction);
                } else {
                    $manager->add($item);
                }
            } catch (Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
            $i++;
        }
        Helper::invalidate();
    }

    /**
     * Assign or remove items
     * @param array $routes
     * @return array
     */
    public function remove($routes)
    {
        $manager = Yii::$app->getAuthManager();
        foreach ($routes as $route) {
            try {
                $item = $manager->createPermission('/' . trim($route, '/'));
                $manager->remove($item);
            } catch (Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
    }

    /**
     * Get avaliable and assigned routes
     * @return array
     */
    public function getRoutes()
    {
        $manager = Yii::$app->getAuthManager();
        
        $app = Yii::$app;
        $apps = $this->getAppList();
        $routes = [];
        foreach ($apps as $value)
        {
            $this->setActiveApp($value);
            $routes = array_merge($routes, $this->getAppRoutes());
        }
        
        Yii::$app = $app;
        $exists = [];
        foreach (array_keys($manager->getPermissions()) as $name) {
            if ($name[0] !== '/') {
                continue;
            }
            $exists[$name] = $routes[$name];
            unset($routes[$name]);
        }
        return[
            'avaliable' => $routes,
            'assigned' => $exists
        ];
    }

    /**
     * Get list of application routes
     * @return array
     */
    public function getAppRoutes($module = null)
    {
        if ($module === null) {
            $module = Yii::$app;
        } elseif (is_string($module)) {
            $module = Yii::$app->getModule($module);
        }
        $key = [__METHOD__, $module->getUniqueId()];
        $cache = Configs::instance()->cache;
        if ($cache === null || ($result = $cache->get($key)) === false) {
            $result = [];
            $this->getRouteRecrusive($module, $result);
            if ($cache !== null) {
                $cache->set($key, $result, Configs::instance()->cacheDuration, new TagDependency([
                    'tags' => self::CACHE_TAG,
                ]));
            }
        }

        return $result;
    }

    /**
     * Get route(s) recrusive
     * @param \yii\base\Module $module
     * @param array $result
     */
    protected function getRouteRecrusive($module, &$result)
    {
        $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            foreach ($module->getModules() as $id => $child) {
                if ((($child = $module->getModule($id)) !== null)&&(!in_array($id, $this->excluded))) {
                    $this->getRouteRecrusive($child, $result);
                }
            }

            $all = '/'.Yii::$app->id.'/'.$module->uniqueId.(($module->uniqueId != '')?'/*':'*');
            $result[$all] = $all;
            $descr = $this->getDescription($module, '', $this::MODULE_TYPE);
            if($descr != null)
                $result[$all] = $descr;
            
            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module, $result);
            }

            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '', $result);
            /*$all = '/' . ltrim($module->uniqueId . '/*', '/');
            $result[$all] = $all;*/
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list controller under module
     * @param \yii\base\Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     * @return mixed
     */
    protected function getControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);
        $token = "Get controllers from '$path'";
        Yii::beginProfile($token, __METHOD__);
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file) && preg_match('%^[a-z0-9_/]+$%i', $file . '/')) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $baseName = substr(basename($file), 0, -14);
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $baseName));
                    $id = ltrim(str_replace(' ', '-', $name), '-');
                    $className = $namespace . $baseName . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $this->getControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list action of controller
     * @param mixed $type
     * @param string $id
     * @param \yii\base\Module $module
     * @param string $result
     */
    protected function getControllerActions($type, $id, $module, &$result)
    {
        $token = "Create controller with cofig=" . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller, $result);
            $all = '/'.Yii::$app->id."/{$controller->uniqueId}/*";
            $result[$all] = $all;
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get route of action
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
     */
    protected function getActionRoutes($controller, &$result)
    {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            $prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $result['/'.Yii::$app->id.$prefix . $id] = '/'.Yii::$app->id.$prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                    $id = $prefix . ltrim(str_replace(' ', '-', $name), '-');
                    $id = '/'.Yii::$app->id.$id;
                    $result[$id] = $id;
                    
                    $descr = $this->getDescription($controller, $method->getName(), $this::ACTION_TYPE);
                    if($descr != null)
                        $result[$id] = $descr;
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Ivalidate cache
     */
    public static function invalidate()
    {
        if (Configs::cache() !== null) {
            TagDependency::invalidate(Configs::cache(), self::CACHE_TAG);
        }
    }

    /**
     * Set default rule of parameterize route.
     */
    protected function setDefaultRule()
    {
        if (Yii::$app->getAuthManager()->getRule(RouteRule::RULE_NAME) === null) {
            Yii::$app->getAuthManager()->add(new RouteRule());
        }
    }
    
    protected function getDescription ($class, $method, $type) 
    {
        $class  = new \ReflectionClass( $class );
        
        if ($type == $this::ACTION_TYPE)
        {
            $method = $class->getMethod( $method );
            $param  = $method->getParameters();
            $doc    = $method->getDocComment();
        }
        if($type == $this::CONTROLLER_TYPE)
            $doc = $class->getDocComment();
        
        if($type == $this::MODULE_TYPE)
        {
            $doc = $class->getDocComment();
        }    
        //Разбираем PHPdoc
        preg_match_all( '/@(\bdescription\ )(.+)\./is', $doc, $arr );

        return ($arr[2][0] != null)?mb_strtoupper(Yii::t('common', Yii::$app->id))." - ".Yii::t('routes',$arr[2][0]): null;
    }
    
    protected function getAppList()
    {
        $i = 0;
        $app = array();
        do
        {
            if(env('APP_'.$i)!= null)
                $app[] = env('APP_'.$i);
            $i++;
        }
        while (env('APP_'.$i)!= null);
        return $app;         
    }
    
    protected function setActiveApp($app_name)
    {
        $config = \yii\helpers\ArrayHelper::merge(
                        require(__DIR__ . '/../../../../common/config/base.php'),
                        require(__DIR__ . '/../../../../common/config/web.php'));
        switch ($app_name)
        {
            case 'frontend':
                   $config = \yii\helpers\ArrayHelper::merge(
                        $config,   
                        require(__DIR__ . '/../../../../frontend/config/base.php'),
                        require(__DIR__ . '/../../../../frontend/config/web.php')
                    );
                    new yii\web\Application($config);
                    break;
            case 'privatezone':
                    //$config1 = require(__DIR__ . '/../../../../privatezone/config/base.php');
                    $config = \yii\helpers\ArrayHelper::merge(
                        $config,
                        require(__DIR__ . '/../../../../privatezone/config/base.php'),
                        require(__DIR__ . '/../../../../privatezone/config/web.php')
                    );
                    new yii\web\Application($config);
                    break;
            case 'storage':
                   $config = require(__DIR__ . '/../../../../storage/config/base.php');
                   new yii\web\Application($config);
                   break;
            default: //$this->active_app = Yii::$app; 
                   // $config = require(__DIR__ . '/../../../../backend/config/base.php');
                    $config = \yii\helpers\ArrayHelper::merge(
                        $config,    
                        require(__DIR__ . '/../../../../backend/config/base.php'),
                        require(__DIR__ . '/../../../../backend/config/web.php')
                    );
                    new yii\web\Application($config);
                    break;    
        }
    }    
}
