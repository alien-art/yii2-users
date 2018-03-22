<?php

namespace alien\users\components;

use alien\users\models\forms\LoginForm;
use alien\users\Module;
use yii\base\Widget;

class AuthorizationWidget extends Widget
{
    public function run()
    {
        $model = new LoginForm;
        Module::registerTranslations();
        return $this->render('authorizationWidget', ['model' => $model]);
    }
}