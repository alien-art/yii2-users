<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use alien\users\UsersAsset;

/* @var $this yii\web\View */
/* @var $routes [] */

$this->title = Yii::t('backend', 'Routes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('users', 'USERS'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('users', 'Access rights management'), 'url' => ['/user/rbac/index']];
$this->params['breadcrumbs'][] = $this->title;

UsersAsset::register($this);
?>
<div class="row">
    <h4><?= Html::encode($this->title) ?></h4>

    <div class="permission-children-editor" style="width: 100%;">
        <?php $form = ActiveForm::begin(['options' => ['style' => "width: 100%;"]]); ?>
        <div class="col-xl-4 col-xs-12 children-list" style="float:left">
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="form-group">
                        <input type="text" class="form-control listFilter"
                               placeholder="<?= Yii::t('users', 'FILTER_PLACEHOLDER') ?>">
                    </div>
                    <?= $form->field($modelForm, 'assigned')->dropDownList(
                        $routes['assigned'],
                        ['multiple' => 'multiple', 'size' => '20'])
                    ?>
                </div>
            </div>
        </div>
        <div class="text-center" style="float:left; margin-top: 15%;">
            <div class="panel panel-info">
                <div class="panel-body">
                    <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="assign"><span
                                class="ion-arrow-left-c" data-pack="default"></span></button>
                    <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="revoke"><span
                                class="ion-arrow-right-c" data-pack="default"></span></button>
                </div>
            </div>
        </div>


        <div class="col-xl-4 col-xs-12 children-list" style="float:left">
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="form-group">
                        <input type="text" class="form-control listFilter"
                               placeholder="<?= Yii::t('users', 'FILTER_PLACEHOLDER') ?>">
                    </div>
                    <?= $form->field($modelForm, 'unassigned')->dropDownList(
                        $routes['avaliable'],
                        ['multiple' => 'multiple', 'size' => '20'])
                    ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>