<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form yii\bootstrap\ActiveForm */
$this->title = Yii::t('backend', 'Edit account')
?>

<div class="modal-header">
    <h5 class="modal-title"><?= Yii::t('backend', 'Edit account') ?></h5>
    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
</div>
<div class="modal-body">
    <div class="user-profile-form">

        <?php $form = ActiveForm::begin(['id' => 'account-form', 'action' => Url::toRoute('/account'), 'options' =>['class' => 'form-validate']]); ?>

        <?php echo $form->field($model, 'username') ?>

        <?php echo $form->field($model, 'email') ?>

        <?php echo $form->field($model, 'password')->passwordInput() ?>

        <?php echo $form->field($model, 'password_confirm')->passwordInput() ?>

        <div class="form-group">
            <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$js = <<<JS
        jQuery('#account-form').on('beforeSubmit', function(){
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
