<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\RoleScoreCardTemplate */

$this->title = 'Create Role Score Card Template';
$this->params['breadcrumbs'][] = ['label' => 'Role Score Card Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-score-card-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
