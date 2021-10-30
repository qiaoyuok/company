<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Team;
use backend\models\Ranking;

/* @var $this yii\web\View */
/* @var $model backend\models\Employee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form box-body">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => FORM_TEMPLATE,
        ]]); ?>

    <?= $form->field($model, 'employee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'team_id')->dropDownList(\yii\helpers\ArrayHelper::map(Team::getTeamList(), 'id', 'name'), ['class' => 'chosen-select', 'style' => 'width:180px']) ?>

    <?= $form->field($model, 'ranking_id')->dropDownList(Ranking::getRankingList(), ['class' => 'chosen-select', 'style' => 'width:180px']) ?>

    <?= $form->field($model, 'joined_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'basic_salary')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0, 'step' => 1]) ?>

    <?= $form->field($model, 'allowance')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0, 'step' => 1]) ?>

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
<script>
    $(document).ready(function () {
        $(".chosen-select").chosen({disable_search_threshold: 10});

    })
</script>