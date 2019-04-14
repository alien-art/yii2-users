<?php

namespace alien\users\controllers;

use alien\users\models\forms\AssignmentForm;
use Yii;
use alien\users\models\Route;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * Description of RuleController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class RouteController extends Controller
{
    public function behaviors()
    {
        return [
        ];
    }
    /**
     * Lists all Route models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Route();
        $modelForm = new AssignmentForm();

        if ($modelForm->load(Yii::$app->request->post())) {
            if($modelForm->action == 'revoke') {
                $model->remove($modelForm->assigned);
            }
            else if($modelForm->action == 'assign')
            {
                $model->addNew($modelForm->unassigned);
            }
            return $this->redirect(Yii::$app->request->url);
        }

        $routes = $model->getRoutes();
        return $this->render($this->module->getCustomView('index'), ['modelForm' => $modelForm, 'routes' => $routes]);
    }
}
