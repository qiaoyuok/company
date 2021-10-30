<?php

use common\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $rankingList */
$rankingList = [];
$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
$th = '<tr>
            <th>Employee Name</th>
            <th>Account</th>
            <th>Role</th>
            <th>Options</th>
        </tr>';
?>
<script>
    $(document).ready(function () {
        $('#ranking-table').DataTable({
            searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
            sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
            bFilter: false,    //去掉搜索框方法三：这种方法可以
            bLengthChange: false,   //去掉每页显示多少条数据方法
            "bInfo": false,
            sort: false,
            paging: false
        });
    });
</script>
<div class="ranking-index box-body">
    <p style="margin: 0 !important;float: right"><?= Html::create(['/base/member/ajax-edit'],'Add', ['class' => 'btn btn-success','data-toggle' => 'modal', 'data-target' => '#ajaxModal',]) ?></p>
    <table id="ranking-table" class="display" style="width:100%">
        <thead>
        <?= $th ?>
        </thead>
        <tbody>
        <?php foreach ($members as $member): ?>
            <tr>
                <td><?= $member['employee'] ?></td>
                <td><?= $member['username'] ?></td>
                <td><?= $member['type'] == 10 ? 'admin' : $member['title'] ?></td>
                <td>
                    <a class="btn btn-danger"
                       title="Delete" aria-label="Delete" data-pjax="0"
                       data-confirm="Are you sure you want to delete this item?" data-method="post"
                       href="<?= Url::to(['/base/member/destroy', 'id' => $member['id']], PHP_URL_SCHEME) ?>">Delete</a>
                    <?= Html::a('Edit', ['/base/member/ajax-edit', 'id' => $member['id']], [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                        'class' => 'btn btn-warning',
                        'type' => 'button'
                    ]) . '<br>';
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
