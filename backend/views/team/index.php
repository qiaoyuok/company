<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $teamList */

$this->title = 'Teams';
$this->params['breadcrumbs'][] = $this->title;
$th = '<tr>
            <th>No</th>
            <th>Team Name</th>
            <th>Updated At</th>
            <th>Options</th>
        </tr>';
?>
<script>
    $(document).ready(function () {
        $('#team-table').DataTable({
            searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
            sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
            bFilter: false,    //去掉搜索框方法三：这种方法可以
            bLengthChange: false,   //去掉每页显示多少条数据方法
        });
    });
</script>
<div class="team-index box-body">
    <div class="box-header col-md-12">
        <form action="<?= Url::current() ?>" method="get" class="form-inline">
            <div class="form-group">
                <label class="control-label">Team Name：</label>
                <input name="teamName" value="<?= $_GET['teamName'] ?? '' ?>" type="text" class="form-control"
                       style="width: 180px;height: 30px" aria-describedby="basic-addon3">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <p style="float: right"><?= Html::a('Create Team', ['create'], ['class' => 'btn btn-success']) ?></p>
        </form>
    </div>
    <table id="team-table" class="display" style="width:100%">
        <thead>
        <?= $th ?>
        </thead>
        <tbody>
        <?php foreach ($teamList as $team): ?>
            <tr>
                <td><?= $team['id'] ?></td>
                <td><?= $team['name'] ?></td>
                <td><?= $team['updated_at'] ?></td>
                <td>
                    <a class="btn btn-warning"
                       href="<?= Url::to(['/team/update', 'id' => $team['id']], PHP_URL_SCHEME) ?>">Edit</a>
                    <a class="btn btn-danger"
                       title="Delete" aria-label="Delete" data-pjax="0"
                       data-confirm="Are you sure you want to delete this item?" data-method="post"
                       href="<?= Url::to(['/team/delete', 'id' => $team['id']], PHP_URL_SCHEME) ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <?= $th ?>
        </tfoot>
    </table>
</div>
