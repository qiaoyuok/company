<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Billings';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    td.details-control {
        background: url('/resources/images/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url('/resources/images/details_close.png') no-repeat center center;
    }
</style>
<div class="billing-index">
    <div class="box-header row">
        <form action="<?= \yii\helpers\Url::to('/billing/index', PHP_URL_SCHEME) ?>" method="get" class="form-inline">
            <div class="form-group">
                <span>Company Name:</span>
                <input name="companyName" value="<?= $companyName ?? '' ?>" type="text" class="form-control"
                       style="width: 180px" aria-describedby="basic-addon3">
            </div>
            <div class="form-group">
                <label class="control-label">Date:</label>
                <?= /** @var string $date */
                DatePicker::widget([
                    'name' => 'date',
                    'id' => 'date-change',
                    'value' => $date,
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm',//yyyy选择到年，yyyy-mm到月，yyyy-mm-dd到天
                        'startView' => 1,    //其实范围（0：日  1：天 2：年）
                        'maxViewMode' => 2,  //最大选择范围（年）
                        'minViewMode' => 1,  //最小选择范围（年）
                    ]
                ]); ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <p style="margin: 0 !important;float: right"><?= Html::a('Create Billing', ['create'], ['class' => 'btn btn-success']) ?></p>
        </form>
    </div>

    <table id="example" class="display" style="width:100%">
        <thead>
        <tr>
            <th></th>
            <th>Date</th>
            <th>Company Name</th>
            <th>Time Sheet</th>
            <th>On Site Allowance</th>
            <th>Billed Amount(RM)</th>
            <th>Approved</th>
            <th>Invoice Number</th>
            <th>Invoice Date</th>
            <th>Options</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th colspan="10" style="text-align:right">Total:</th>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    /* Formatting function for row details - modify as you need */
    function format(d) {
        console.log(d, 8889);
        // `d` is the original data object for the row
        return '<table class="display dataTable no-footer no-border" style="width: 70%;" role="grid" aria-describedby="example_info">' +
            '<tr>' +
            '<td style="font-weight: bold">Reimbursement:</td>' +
            '<td>Price:</td>' +
            '<td>RM ' + d[0].price + '</td>' +
            '<td>Description:</td>' +
            '<td>' + d[0].description + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td style="font-weight: bold">Fee:</td>' +
            '<td>Price:</td>' +
            '<td>RM ' + d[1].price + '</td>' +
            '<td>Description:</td>' +
            '<td>' + d[1].description + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td style="font-weight: bold">Out of Pocket:</td>' +
            '<td>Price:</td>' +
            '<td>RM ' + d[2].price + '</td>' +
            '<td>Description:</td>' +
            '<td>' + d[2].description + '</td>' +
            '</tr>' +
            '</table>';
    }

    function approved(id) {
        if (confirm('Are you sure?')) {
            $.get('/billing/approved?id=' + id, function (res) {
                layer.msg(res.msg)
                if (res.status == 1) {
                    window.location.reload()
                }
            })
        }
    };
    $(document).ready(function () {
        var table = $('#example').DataTable({
            data: <?= /** @var array $data */
                \yii\helpers\Json::encode($data) ?>,
            searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
            sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
            bFilter: false,    //去掉搜索框方法三：这种方法可以
            bLengthChange: false,   //去掉每页显示多少条数据方法
            "columns": [
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
                {"data": "date"},
                {"data": "company_name"},
                {"data": "time_sheet"},
                {"data": "on_site_allowance"},
                {"data": "billed_amount"},
                {"data": "approved_at"},
                {"data": "invoice_number"},
                {"data": "invoice_date"},
                {
                    "orderable": false,
                    "data": null,
                    "render": function (data) {
                        return (data.is_approved != 1 ? '<button type="button" class="btn btn-danger" onClick="approved(' + data.id + ')">Approved</button>&nbsp;&nbsp;' +
                            '<a type="button" class="btn btn-info" href="/billing/update?id=' + data.id + '">Edit Billed Amount</a>' : '<button type="button" class="btn btn-info">Has Approved</button>')
                    }
                },
            ],
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total over this page
                pageTotal = api
                    .column(5, {page: 'current'})
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(5).footer()).html('Total:' + '<span style="display: inline-block;width: 60px">' + pageTotal + '</span>');
            }
        });

        // Add event listener for opening and closing details
        $('#example tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                if (row.data().billed_amount_detail) {
                    row.child(format(row.data().billed_amount_detail)).show();
                    tr.addClass('shown');
                } else {
                    layer.msg("There is no amount detail")
                }
            }
        });
    });
</script>