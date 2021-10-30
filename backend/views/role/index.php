<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $list */

$this->title = 'Role Listing';
$this->params['breadcrumbs'][] = $this->title;
$th = '<tr>
            <th>Role Name</th>
            <th>Options</th>
        </tr>';
?>
<script>
    $(document).ready(function () {
        $.extend(true, $.fn.dataTable.defaults, {
            "searching": false
        });
        $('#ranking-table').DataTable({
            "dom": '<"toolbar">frtip',
            'paging': false,
        });
        $("div.toolbar").html('');
    });
</script>
<div class="ranking-index box-body">
    <p style="margin: 0 !important;float: right"><?= Html::a('Create Role', ['create'], ['class' => 'btn btn-success']) ?></p>
    <table id="ranking-table" class="display" style="width:100%">
        <thead>
        <?= $th ?>
        </thead>
        <tbody>
        <?php foreach ($list as $item): ?>
            <tr>
                <td><?= $item['role_name'] ?></td>
                <td>
                    <a class="btn btn-warning"
                       href="<?= Url::to(['update', 'id' => $item['id']], PHP_URL_SCHEME) ?>">Edit</a>
                    <a class="btn btn-danger"
                       title="Delete" aria-label="Delete" data-pjax="0"
                       data-confirm="Are you sure you want to delete this item?" data-method="post"
                       href="<?= Url::to(['delete', 'id' => $item['id']], PHP_URL_SCHEME) ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
