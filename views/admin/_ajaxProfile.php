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

<div class="modal-header">
    <h5 class="modal-title"><?= Yii::t('backend', 'Edit profile') ?></h5>
    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
</div>
<div class="modal-body">
    <div class="user-profile-form">
        <?php $form = ActiveForm::begin(['id' => 'profile-form', 'action' => Url::toRoute('/profile'), 'options' =>['class' => 'form-validate']]); ?>
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
</div>
<?php
$js = <<<JS
        jQuery('#profile-form').on('beforeSubmit', function(){
var form = jQuery(this);
Pace.ignore(function(){
    $.ajax({
    url: form.attr("action"),
    type: form.serialize(),
    data: data,
    success: function(result) {
        if(result != 'sended')
        form.parent().replaceWith(result);
        else
        $('#modal').modal('hide');
        },
    error: function() {
        console.log("server error");
        }   
    });
});
return false;
});

JS;
$this->registerJs($js, \yii\web\View::POS_END, 'ajax_form');
?>