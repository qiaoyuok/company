<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $rankingList */

$this->title = 'Ranking Listing';
$this->params['breadcrumbs'][] = $this->title;
$th = '<tr>
            <th>Ranking</th>
            <th>Commission %</th>
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
    <p style="margin: 0 !important;float: right"><?= Html::a('Create Ranking', ['create'], ['class' => 'btn btn-success']) ?></p>
    <table id="ranking-table" class="display" style="width:100%">
        <thead>
        <?= $th ?>
        </thead>
        <tbody>
        <?php foreach ($rankingList as $ranking): ?>
            <tr>
                <td><?= $ranking['ranking'] ?></td>
                <td><?= $ranking['commission'] ?></td>
                <td><?= $ranking['title'] ?></td>
                <td>
                    <a class="btn btn-warning"
                       href="<?= Url::to(['/ranking/update', 'id' => $ranking['id']], PHP_URL_SCHEME) ?>">Edit</a>
                    <a class="btn btn-danger"
                       title="Delete" aria-label="Delete" data-pjax="0"
                       data-confirm="Are you sure you want to delete this item?" data-method="post"
                       href="<?= Url::to(['/ranking/delete', 'id' => $ranking['id']], PHP_URL_SCHEME) ?>">Delete</a>
                    <a class="btn <?= $ranking['sctId'] ? 'btn-success' : 'btn-info' ?>" href="<?=
                    Url::to(['/score-card/template-config',
                        'rankingId' => $ranking['id'],
                        'rankingName' => $ranking['ranking']], PHP_URL_SCHEME) ?>"><?= $ranking['sctId'] ? 'Edit Score Card Template' : 'Create Score Card Template' ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
