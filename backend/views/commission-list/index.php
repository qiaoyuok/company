<?php

use yii\helpers\Json;
use kartik\date\DatePicker;

?>


<div id="commission-list-container">
    <div class="box-header row">
        <form action="<?= \yii\helpers\Url::to('/commission-list/index', PHP_URL_SCHEME) ?>" method="get"
              class="form-inline">
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
        </form>
    </div>

    <!--    group-->
    <table id="commission-list" class="display" style="margin: 0 !important">
        <thead>
        <tr>
            <th>Company</th>
            <th>Team</th>
            <th>Employee</th>
            <th>Position</th>
            <th>Amount</th>
            <th>Timesheet</th>
            <th>Score Card</th>
            <th>Commission</th>
            <th>Incentive</th>
            <th>Basic Salary</th>
            <th>Allowance</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
    var scoreCardVm;
    $(document).ready(function () {
        scoreCardVm = new Vue({
            el: "#commission-list-container",
            data: {
                data: <?= /** @var array $data */ Json::encode($data ?? []); ?>,
                year: "<?= $year ?? date('Y') ?>"
            },
            created() {
                var _th = this
                this.$nextTick(function () {
                    _th.createTable()
                })
            },
            methods: {
                createTable() {
                    var dateObj = new Date();
                    var date = dateObj.getFullYear()+'-'+(dateObj.getMonth()+1)+'-'+dateObj.getDate();
                    var _th = this
                    $('#commission-list').DataTable({
                        dom: 'Bfrtip',
                        data: _th.data,
                        "columns": [
                            {"data": "company_name"},
                            {"data": "team_name"},
                            {"data": "employee"},
                            {"data": "ranking"},
                            {"data": "amount"},
                            {"data": "time_sheet"},
                            {"data": "score"},
                            {"data": "commission"},
                            {"data": "all_incentive"},
                            {"data": "basic_salary"},
                            {"data": "all_allowance"},
                            {"data": "total"},
                        ],
                        buttons: [
                            {
                                extend: 'excel',
                                text: 'Save current page',
                                filename: 'commission-page' + date,
                                exportOptions: {
                                    modifier: {
                                        page: 'current'
                                    }
                                }
                            },
                            {
                                extend: 'excel',
                                text: 'Save all data',
                                filename: 'commission-all' + date,
                                exportOptions: {
                                    page: 'all'
                                }
                            },
                        ],
                        searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
                        // sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
                        // bFilter: false,    //去掉搜索框方法三：这种方法可以
                        // bLengthChange: false,   //去掉每页显示多少条数据方法
                        "bInfo": false,
                        sort: false,
                    });
                }
            }
        });

    });
</script>