<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Ranking */

$this->title = 'Create Ranking';
$this->params['breadcrumbs'][] = ['label' => 'Rankings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ranking-create box-body">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
