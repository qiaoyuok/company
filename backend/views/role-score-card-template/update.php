<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\RoleScoreCardTemplate */

$this->title = 'Update Role Score Card Template: ' . $model->score_card_template_id;
$this->params['breadcrumbs'][] = ['label' => 'Role Score Card Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->score_card_template_id, 'url' => ['view', 'id' => $model->score_card_template_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="role-score-card-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
