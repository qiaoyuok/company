<?php

use backend\assets\ChosenAsset;
use yii\helpers\Url;
use common\helpers\Html;
use yii\helpers\Json;
use kartik\date\DatePicker;
use backend\models\Team;

?>


<div class="row" id="time-sheet-container">
    <div class="box-header col-md-12">
        <form action="<?= Url::current() ?>" method="get" class="form-inline">
            <div class="form-group">
                <label class="control-label">Company Name：</label>
                <input name="company_name" value="<?= /** @var string $companyName */
                $companyName ?>" type="text" class="form-control"
                       style="width: 180px;height: 30px" aria-describedby="basic-addon3">
            </div>
            <div class="form-group col-md-3" style="display: flex;align-items: center">
                <label class="control-label">Date：</label>
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
                <label class="control-label">Team：</label>
                <?= Html::dropDownList('team_id', $teamId ?? null, \yii\helpers\ArrayHelper::map(Team::getTeamList(), 'id', 'name'), [
                    'class' => 'chosen-select',
                    'style' => "width:180px",
                    'id' => 'team-change',
                    'prompt' => 'Please Select Team'
                ]) ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>

    <div class="col-md-12">
        <table id="time-sheet" class="display" style="margin: 0 !important">
            <thead>
            <tr>
                <th>No</th>
                <th>Company Name</th>
                <th>Employee</th>
                <th>Total Time</th>
                <th>On Site</th>
                <th>Allowance</th>
                <th>Option</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(timeSheet,i) in timeSheets">
                <td>{{timeSheet.approved_id}}</td>
                <td>{{timeSheet.company_name}}</td>
                <td>{{timeSheet.employee}}</td>
                <td>{{timeSheet.total_hours}}</td>
                <td>{{timeSheet.total_on_site}}</td>
                <td style="display: flex;align-items: center;justify-content: center">
                    <input style="width: 100px" type="number" v-model="timeSheets[i].allowance"
                           :disabled="!timeSheets[i].edit">

                    <span style="margin-left: 5px;cursor: pointer"
                          @click="save(timeSheets[i].approved_id,'allowance',timeSheets[i].allowance)"
                          v-if="timeSheets[i].edit && timeSheets[i].allowance!=timeSheets[i].allowanceOrigin">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </span>
                </td>
                <td>
                    <button v-if="timeSheets[i].is_approved!=1" type="button" class="btn btn-warning"
                            @click="editStatus(i)">
                        {{timeSheets[i].edit ? 'Cancel' : 'Edit'}}
                    </button>
                    <button type="button" class="btn btn-info" v-if="timeSheets[i].is_approved==1">Has Approved</button>
                    <button type="button" class="btn btn-danger" v-else
                            @click="save(timeSheets[i].approved_id,'is_approved',1)">
                        Approved
                    </button>
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
            el: "#time-sheet-container",
            data: {
                'timeSheets': <?= /** @var array $data */Json::encode($data) ?>
            },
            methods: {
                editStatus(i) {
                    this.timeSheets[i].edit = !this.timeSheets[i].edit
                    if (!this.timeSheets[i].edit) {
                        this.timeSheets[i].allowance = this.timeSheets[i].allowanceOrigin
                    }
                },
                savePost(id, field, value) {
                    $.post('/time-sheet/save', {id: id, field: field, value: value}, function (res) {
                        layer.msg(res.msg)
                        if (res.status == 1) {
                            window.location.reload()
                        }
                    })
                },
                save(id, field, value) {
                    if (field === 'is_approved' && value === 1) {
                        if (confirm("This operation is not reversible")) {
                            this.savePost(id, field, value)
                        }
                    } else {
                        this.savePost(id, field, value)
                    }
                }
            }
        });

        $(".chosen-select").chosen({max_selected_options: 5});
        $('#time-sheet').DataTable({
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