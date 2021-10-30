<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $companyList */

$this->title = 'Companies';
$this->params['breadcrumbs'][] = $this->title;
$th = '<tr>
            <th>No</th>
            <th>Company Code</th>
            <th>Company Name</th>
            <th>Company PIC</th>
            <th>Contact Number</th>
            <th>On Site / Off Site</th>
            <th>Team</th>
            <th>Person Assigned</th>
            <th>Options</th>
        </tr>';
?>
<script>
    $(document).ready(function () {
        $.extend(true, $.fn.dataTable.defaults, {
            "searching": false
        });
        $('#company-table').DataTable({
            "dom": '<"toolbar">frtip'
        });
    });
</script>
<style>
    form{
        display: flex;
    }
    form .input-group-item{
        display: flex;
        align-items: center;
    }
    form .input-group-item:nth-child(even){
        margin: 0 25px;
    }
    form .input-group-item span{
        padding: 0 10px;
    }
</style>
<div class="company-index box-body">
    <div class="box-header row" style="display: flex;">
        <form action="<?= Url::to('/company/index', PHP_URL_SCHEME) ?>" method="get" style="flex-grow: 1">
            <div class="input-group-item">
                <span>Company Name:</span>
                <input name="company_name" value="<?= $_GET['company_name']??'' ?>" type="text" class="form-control" style="width: 180px" aria-describedby="basic-addon3">
            </div>
            <div class="input-group-item">
                <span>Company Number:</span>
                <input name="company_number" value="<?= $_GET['company_number']??'' ?>" type="text" class="form-control" style="width: 180px" aria-describedby="basic-addon3">
            </div>
            <div class="input-group-item">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <p style="margin: 0 !important;"><?= Html::a('Create Company', ['create'], ['class' => 'btn btn-success']) ?></p>
    </div>

    <table id="company-table" class="display" style="width:100%">
        <thead>
        <?= $th ?>
        </thead>
        <tbody>
        <?php foreach ($companyList as $company): ?>
            <tr>
                <td><?= $company['id'] ?></td>
                <td><?= $company['company_code'] ?></td>
                <td><?= $company['company_name'] ?></td>
                <td><?= $company['company_pic'] ?></td>
                <td><?= $company['company_number'] ?></td>
                <td><?= $company['is_on_site'] ? 'ON' : 'OFF' ?></td>
                <td><?= $company['team_name'] ?></td>
                <td>
                    <?php foreach ($company['person_assign'] as $employeeId): ?>

                    <?=  \yii\helpers\ArrayHelper::map(\backend\models\Employee::getEmployeeList(),'id','employee')[$employeeId].','??''  ?>

                    <?php endforeach;?>
                </td>
                <td>
                    <a class="btn btn-warning"
                       href="<?= Url::to(['/company/update', 'id' => $company['id']], PHP_URL_SCHEME) ?>">Edit</a>
                    <a class="btn btn-danger"
                       title="Delete" aria-label="Delete" data-pjax="0"
                       data-confirm="Are you sure you want to delete this item?" data-method="post"
                       href="<?= Url::to(['/company/delete', 'id' => $company['id']], PHP_URL_SCHEME) ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
