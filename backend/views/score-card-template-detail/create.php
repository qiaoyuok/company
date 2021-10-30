<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScoreCardTemplateDetail */

$this->title = 'Create Score Card Template Detail';
$this->params['breadcrumbs'][] = ['label' => 'Score Card Template Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-card-template-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
