<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $rewardList */

$this->title = 'Companies';
$this->params['breadcrumbs'][] = $this->title;
$th = '<tr>
            <th>Date</th>
            <th>Photo</th>
            <th>Type</th>
            <th>Reward Name</th>
            <th>Member</th>
            <th>Options</th>
        </tr>';
$cashType = [1 => 'Cash', 2 => "No Cash"];
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
        <form action="<?= Url::to('/reward/index', PHP_URL_SCHEME) ?>" method="get" style="flex-grow: 1">
            <div class="input-group-item">
                <span>Date:</span>
                <?=
                DatePicker::widget([
                    'name' => 'date',
                    'id' => 'year',
                    'value' => $_GET['date']??date("Y-m-d"),
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'format' => 'yyyy-m-dd',
                        'multidate' => false,
                    ]
                ]); ?>
            </div>
            <div class="input-group-item">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <p style="margin: 0 !important;"><?= Html::a('Create Reward', ['create'], ['class' => 'btn btn-success']) ?></p>
    </div>

    <table id="company-table" class="display" style="width:100%">
        <thead>
        <?= $th ?>
        </thead>
        <tbody>
        <?php foreach ($rewardList as $reward): ?>
            <tr>
                <td><?= $reward['date'] ?></td>
                <td><img style="width: 80px;height: 30px" src="<?= $reward['photo'] ?>" alt=""></td>
                <td><?= $cashType[$reward['Type']]??'/' ?></td>
                <td><?= $reward['reward_name'] ?></td>
                <td><?= $reward['members'] ?></td>
                <td>
                    <a class="btn btn-warning"
                       href="<?= Url::to(['/reward/update', 'id' => $reward['id']], PHP_URL_SCHEME) ?>">Edit</a>
                    <a class="btn btn-danger"
                       title="Delete" aria-label="Delete" data-pjax="0"
                       data-confirm="Are you sure you want to delete this item?" data-method="post"
                       href="<?= Url::to(['/reward/delete', 'id' => $reward['id']], PHP_URL_SCHEME) ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <?= $th ?>
        </tfoot>
    </table>
</div>
