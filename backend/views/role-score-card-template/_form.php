<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Role;

/* @var $this yii\web\View */
/* @var $model backend\models\RoleScoreCardTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="role-score-card-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'role_id')->dropDownList(Role::getRoleList(), ['prompt' => 'Select Role']) ?>

    <?= $form->field($model, 'template_name')->textInput(['maxlength' => true,'required'=>true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>