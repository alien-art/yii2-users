<?php

namespace alien\users;

use Yii;

class Module extends \yii\base\Module
{
    public $alias = '@alien';

    public $controllerNamespace = 'alien\users\controllers';

    public $userPhotoUrl = '';

    public $userPhotoPath = '';

    public $customViews = [];

    public $customMailViews = [];

    public $loginLayout = '';

    public function init()
    {
        parent::init();

        $this->setAliases([
            $this->alias => __DIR__,
        ]);

        self::registerTranslations();
    }

    public static function registerTranslations()
    {
        // set alias
        if (!isset(Yii::$app->i18n->translations['users']) && !isset(Yii::$app->i18n->translations['users/*'])) {
            Yii::$app->i18n->translations['users'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@alien/users/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'users' => 'users.php'
                ]
            ];
        }
    }

    public function getCustomView($default)
    {
        if (isset($this->customViews[$default])) {
            return $this->customViews[$default];
        } else {
            return $default;
        }
    }

    public function getCustomMailView($default)
    {
        if (isset($this->customMailViews[$default])) {
            return $this->customMailViews[$default];
        } else {
            return '@alien/users/mail/' . $default;
        }
    }
}
