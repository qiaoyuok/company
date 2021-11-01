<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Create Billing';
$this->params['breadcrumbs'][] = ['label' => 'Billings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="billing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'teamList' => \backend\models\Team::getTeamList(),
        'companyList' => \backend\models\Company::getCompanyListByTeam(0),
    ]) ?>

</div>
