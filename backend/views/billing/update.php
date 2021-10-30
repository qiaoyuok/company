<?php

use backend\models\Company;
use backend\models\Team;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $billingInfo backend\models\Billing */

$this->title = 'Update Billing: ' . $billingInfo['id'];
$this->params['breadcrumbs'][] = ['label' => 'Billings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $billingInfo['id'], 'url' => ['view', 'id' => $billingInfo['id']]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="billing-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= /** @var array $billingInfo */
    $this->render('_form', [
        'billingInfo' => $billingInfo,
        'teamList' => Team::getTeamList(),
        'companyList' => Company::getCompanyListByTeam($billingInfo['team_id']),
    ]) ?>

</div>
