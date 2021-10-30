<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Company;

/* @var $this yii\web\View */
/* @var $model backend\models\Team */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="team-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => FORM_TEMPLATE,
        ]]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

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
