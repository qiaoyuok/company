<?php

use yii\helpers\Html;
use yii\helpers\Url;
use \backend\models\Team;

/* @var $this yii\web\View */
/* @var $teamName */
/* @var $status */
/* @var $employee */
/* @var $employeeList */
/* @var $teamId */

$this->title = 'Employees';
$this->params['breadcrumbs'][] = $this->title;
$th = '<tr>
            <th>No</th>
            <th>Account</th>
            <th>Employee Name</th>
            <th>Team</th>
            <th>Ranking</th>
            <th>Joinned Date</th>
            <th>Basic Salary</th>
            <th>Allowance</th>
            <th>Commission Accumulative</th>
            <th>Options</th>
        </tr>';
?>
<script>
    $(document).ready(function () {
        $.extend(true, $.fn.dataTable.defaults, {
            "searching": false
        });
        $('#employee-table').DataTable({
            "dom": '<"toolbar">frtip'
        });
    });
</script>
<style>
    form {
        display: flex;
    }

    form .input-group-item {
        display: flex;
        align-items: center;
    }

    form .input-group-item:nth-child(even) {
        margin: 0 25px;
    }

    form .input-group-item span {
        padding: 0 10px;
    }

    .select2-container .select2-selection--single {
        height: 34px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered,
    .select2-container--default .select2-selection--single .select2-selection__arrow,
    .select2-container--default .select2-selection--single .select2-selection__clear {
        line-height: 34px;
        height: 34px;
    }
</style>
<div class="employee-index box-body">
    <div class="box-header row" style="display: flex;">
        <form action="<?= Url::to('/employee/index', PHP_URL_SCHEME) ?>" method="get" style="flex-grow: 1">
            <div style="display:flex;width: 240px;height: 34px;align-items: center">
                <span>Team:&nbsp;</span>
                <div style="width: 190px;">
                    <select name="team_id" class="team-selected form-control" aria-required="true">
                        <option value="0">All</option>
                        <?php foreach (\yii\helpers\ArrayHelper::map(Team::getTeamList(), 'id', 'name') as $teamIdK => $teamName): ?>
                            <option value="<?= $teamIdK ?>" <?= $teamId == $teamIdK ? 'selected' : '' ?>><?= $teamName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="input-group-item">
                <span>Employee Name:</span>
                <input name="employee" value="<?= $employee ?>" type="text" class="form-control"
                       style="width: 180px" aria-describedby="basic-addon3">
            </div>
            <div class="input-group-item">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
    <ul class="nav nav-tabs">
        <li role="presentation" class="<?= $status == 1 ? 'active' : '' ?>"><a
                    href="<?= Url::to(['/employee/index', 'status' => 1], PHP_URL_SCHEME) ?>">Active</a></li>
        <li role="presentation" class="<?= $status == 2 ? 'active' : '' ?>"><a
                    href="<?= Url::to(['/employee/index', 'status' => 2], PHP_URL_SCHEME) ?>">Deactivated</a></li>
    </ul>
    <table id="employee-table" class="display" style="width:100%">
        <thead>
        <?= $th ?>
        </thead>
        <tbody>
        <?php foreach ($employeeList as $employee): ?>
            <tr>
                <td><?= $employee['id'] ?></td>
                <td width="8%"><?= $employee['account'] ?></td>
                <td width="8%"><?= $employee['employee'] ?></td>
                <td width="8%"><?= $employee['team_name'] ?></td>
                <td><?= \backend\models\Ranking::getRankingList()[$employee['ranking_id']] ?? '' ?></td>
                <td width="8%"><?= date("d.m.Y", strtotime($employee['joined_at'])) ?></td>
                <td width="8%"><?= "RM" . number_format($employee['basic_salary'], 2) ?></td>
                <td width="8%"><?= "RM" . number_format($employee['allowance'], 2) ?></td>
                <td width="8%"><?= "RM" . number_format($employee['commission_ccumulative'], 2) ?></td>
                <td>
                    <?php if ($employee['id']!=1):?>
                        <a class="btn btn-danger"
                           title="Delete" aria-label="Delete" data-pjax="0"
                           data-confirm="Are you sure you want to delete this item?" data-method="post"
                           href="<?= Url::to(['/employee/delete', 'id' => $employee['id']], PHP_URL_SCHEME) ?>">Delete</a>
                        <a class="btn btn-info"
                           title="Update" aria-label="Update" data-pjax="0"
                           data-confirm="Are you sure you want to <?= $status == 1 ? 'close' : 'open' ?> this item?"
                           data-method="post"
                           href="<?= Url::to(['/employee/status', 'id' => $employee['id'], 'status' => $status == 1 ? 2 : 1], PHP_URL_SCHEME) ?>"><?= $status == 1 ? 'Deactivate' : 'Activate' ?></a>
                    <?php endif;?>
                    <a class="btn btn-warning"
                       href="<?= Url::to(['/employee/update', 'id' => $employee['id']], PHP_URL_SCHEME) ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $(".team-selected").chosen({disable_search_threshold: 10});
    })
</script>
