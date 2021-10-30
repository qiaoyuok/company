<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\ScoreCardTemplateDetail;

/* @var $this yii\web\View */
/* @var $model backend\models\ScoreCardTemplateDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="score-card-template-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'description')->textarea() ?>

    <?= $form->field($model, 'sort')->textInput(['type'=>"number",'value'=>0,'step'=>1]) ?>

    <?= $form->field($model, 'parent_id')->dropDownList(ScoreCardTemplateDetail::getTopDetail()) ?>

    <?= $form->field($model, 'is_part_title')->radioList(['1'=>"Yes",'2'=>"No"]) ?>

    <?= $form->field($model, 'total_type')->radioList(['0'=>"No",'1'=>"Total A",'2'=>'Total B','3'=>"total A+B"]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
