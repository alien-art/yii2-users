<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use budyaga\cropper\Widget;
use common\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model alien\users\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="site-signup">
    <?php $form = ActiveForm::begin(['id' => 'form-profile']); ?>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $form->field($model, 'avatar_path')->widget(Widget::className(), [
                        'uploadUrl' => Url::toRoute('/user/user/uploadPhoto'),
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div><?= $form->field($model, 'username') ?></div>
            <div><?= $form->field($model, 'email')->input('email') ?></div>
            <div><?= $form->field($model, 'sex')->dropDownList(User::getSexArray())?></div>
            <div><?php echo $form->field($model, 'firstname')->textInput(['maxlength' => 255]) ?></div>

            <div><?php echo $form->field($model, 'middlename')->textInput(['maxlength' => 255]) ?></div>

            <div><?php echo $form->field($model, 'lastname')->textInput(['maxlength' => 255]) ?></div>

            <div><?php echo $form->field($model, 'locale')->dropDownlist(Yii::$app->params['availableLocales']) ?></div>
            <div><?= $form->field($model, 'password')->passwordInput() ?></div>
            <div><?= $form->field($model, 'password_repeat')->passwordInput() ?></div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('users', 'CREATE'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

    