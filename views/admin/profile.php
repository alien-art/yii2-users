<?php

use common\models\UserProfile;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use budyaga\cropper\Widget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Edit profile')
?>

<div class="user-profile-form">
    <?php $form = ActiveForm::begin(); ?>
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
            <div><?= $form->field($model, 'firstname')->textInput(['maxlength' => 255]) ?></div>
            <div><?= $form->field($model, 'middlename')->textInput(['maxlength' => 255]) ?></div>
            <div><?= $form->field($model, 'lastname')->textInput(['maxlength' => 255]) ?></div>
            <div><?= $form->field($model, 'locale')->dropDownlist(Yii::$app->params['availableLocales']) ?></div>
            <div><?= $form->field($model, 'sex')->dropDownList(UserProfile::getSexArray()); ?></div>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('users', 'CREATE') : Yii::t('users', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>