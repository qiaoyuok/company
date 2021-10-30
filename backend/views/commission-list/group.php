<?php

use backend\assets\ChosenAsset;
use yii\helpers\Url;
use common\helpers\Html;
use yii\helpers\Json;
use kartik\date\DatePicker;

?>


<div id="commission-team-container">
    <div class="box-header col-md-12">
        <form action="<?= Url::current() ?>" method="get" class="form-inline">
            <div class="form-group">
                <label class="control-label">Team：</label>
                <?= /** @var integer $teamId */
                Html::dropDownList('team_id', $teamId, \yii\helpers\ArrayHelper::map(\backend\models\Team::getTeamList(), 'id', 'name'), [
                    'class' => 'chosen-select team-change',
                    'style' => "width:180px",
                    'id' => 'team-change',
                    'prompt' => 'Please Select Team'
                ]) ?>
            </div>
            <div class="form-group">
                <label class="control-label">Date:</label>
                <?= /** @var string $date */
                DatePicker::widget([
                    'name' => 'date',
                    'id' => 'date-change',
                    'value' => $date ?? '',
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
            <br>
            <br>
            <div class="form-group">
                <label class="control-label">Group Incentive to assign：</label>
                <input disabled value="<?= $Incentive ?>" type="text"
                       class="form-control"
                       style="width: 180px;height: 30px" aria-describedby="basic-addon3">
            </div>
        </form>
    </div>

    <div class="col-md-12">
        <table id="commission-list" class="display" style="margin: 0 !important">
            <thead>
            <tr>
                <th>Team</th>
                <th>Employee</th>
                <th>Position</th>
                <th>Amount</th>
                <th>Option</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,i) in employees">
                <td>{{employees[i].name}}</td>
                <td>{{employees[i].employee}}</td>
                <td>{{employees[i].ranking}}</td>
                <td>
                    <input type="number" v-model="employees[i].amount" :disabled="!employees[i].editStatus">
                    <span style="margin-left: 5px;cursor: pointer"
                          @click="save(item.employee_id,employees[i].amount)"
                          v-if="employees[i].editStatus && employees[i].amount!=employees[i].tmpAmount">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </span>
                </td>
                <td>
                    <span v-if="employees[i].is_approved!=1">
                        <button type="button" class="btn btn-warning" v-if="!employees[i].editStatus"
                                @click="employees[i].editStatus=!employees[i].editStatus">Edit
                    </button>
                    <button type="button" class="btn btn-primary" v-else
                            @click="employees[i].editStatus=!employees[i].editStatus">Cancel
                    </button>
                    </span>
                    <span v-if="employees[i].is_approved!==null">
                        <button v-if="employees[i].is_approved==0"
                                type="button"
                                @click="approved(employees[i].id)"
                                class="btn btn-danger">Approved</button>
                    <button v-else type="button" class="btn btn-info">Has Approved</button>
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    var scoreCardVm;
    $(document).ready(function () {
        scoreCardVm = new Vue({
            el: "#commission-team-container",
            data: {
                employees: <?= /** @var array $data */ Json::encode($employees ?? []); ?>,
                Incentive: "<?= $Incentive ?? 0 ?>",
                quarter: "<?= $quarter ?? 0 ?>",
                year: "<?= $year ?? 0 ?>",
            },
            methods: {
                save(employee_id, amount) {
                    var _th = this
                    $.get('save-group-amount', {
                        employeeId: employee_id,
                        amount: amount,
                        year: _th.year,
                        quarter: _th.quarter
                    }, function (res) {
                        console.log(res, 98);
                        layer.msg(res.msg)
                        if (res.status == 1) {
                            window.location.reload()
                        }
                    })
                },
                approved(id) {
                    var _th = this
                    $.get('group-approved', {
                        id: id
                    }, function (res) {
                        console.log(res, 98);
                        layer.msg(res.msg)
                        if (res.status == 1) {
                            window.location.reload()
                        }
                    })
                }
            }
        });

        $('#commission-list').DataTable({
            searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
            sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
            bFilter: false,    //去掉搜索框方法三：这种方法可以
            bLengthChange: false,   //去掉每页显示多少条数据方法
            "bInfo": false,
            sort: false,
        });
    });
</script>