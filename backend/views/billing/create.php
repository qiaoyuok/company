<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Billing */

$this->title = 'Create Billing';
$this->params['breadcrumbs'][] = ['label' => 'Billings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="billing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'teamList' => \backend\models\Team::getTeamList(),
        'companyList' => \backend\models\Company::getCompanyListByTeam(0),
    ]) ?>

</div>
