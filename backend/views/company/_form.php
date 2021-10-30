<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Team;
use backend\models\Employee;

/* @var $this yii\web\View */
/* @var $model backend\models\Company */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="company-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => FORM_TEMPLATE,
        ]]); ?>

    <?= $form->field($model, 'company_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'team_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(Team::getTeamList(), 'id', 'name'),
        [
            'prompt' => 'Please Select Team',
            'class' => 'chosen-select chosen-team-change',
            'style' => 'width:180px'
        ]) ?>

    <?= $form->field($model, 'person_assign')->dropDownList(
        $model->team_id != 0 ? \yii\helpers\ArrayHelper::map(Employee::getEmployeeListByTeamId($model->team_id), 'id', 'employee') : [],
        [
            "multiple" => "multiple",
            'class' => 'chosen-select chosen-employee-list',
            'style' => 'width:380px'])
    ?>

    <?= $form->field($model, 'is_on_site')->checkbox(['style' => "width:20px;height:20px;"]) ?>

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
