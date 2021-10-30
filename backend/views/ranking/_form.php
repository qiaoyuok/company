<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Ranking */
/* @var $form yii\widgets\ActiveForm */

$roleList = \common\models\rbac\AuthRole::find()
->where(['status'=>1])
->select(['id','title'])
->all();
?>

<div class="ranking-form box-body">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => FORM_TEMPLATE,
        ]]); ?>

<!--    --><?//= Html::dropDownList('team_id', $_GET['team_id'] ?? null, \yii\helpers\ArrayHelper::map(Team::getTeamList(), 'id', 'name'), [
//        'class' => 'chosen-select',
//        'style' => "width:180px",
//        'id' => 'team-change',
//        'prompt' => 'Please Select Team'
//    ]) ?>

    <?= $form->field($model, 'ranking')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'commission')->input('number') ?>

    <?= $form->field($model, 'role_id')->dropDownList(\yii\helpers\ArrayHelper::map($roleList,'id','title'),[
        'class' => 'chosen-select',
        'style' => "width:180px",
        'prompt' => 'Please Select Role'
    ]) ?>

    <div class="form-group">
        <div class='col-xs-3 col-sm-2 text-right'></div>
        <div class='col-xs-9 col-sm-7'>
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <button type="button" class="btn btn-info" onclick="window.history.back()">Back</button>
        </div>
        <div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
