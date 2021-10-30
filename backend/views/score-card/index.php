<?php

use yii\helpers\Url;
use common\helpers\Html;
use yii\helpers\Json;
use kartik\date\DatePicker;
use backend\models\Team;


$year = $_GET['year'] ?? date("Y");

?>

<style>
    table.dataTable thead th, table.dataTable td {
        width: 150px !important;
    }

    table.dataTable {
        width: auto !important;
    }
</style>

<div id="score-card-container">
    <div class="box-header col-md-12">
        <form action="<?= Url::current() ?>" method="get" class="form-inline">
            <div class="form-group">
                <label class="control-label">Team：</label>
                <?= Html::dropDownList('team_id', $_GET['team_id'] ?? null, \yii\helpers\ArrayHelper::map(Team::getTeamList(), 'id', 'name'), [
                    'class' => 'chosen-select',
                    'style' => "width:180px",
                    'id' => 'team-change',
                    'prompt' => 'Please Select Team'
                ]) ?>
            </div>
            <div class="form-group">
                <label class="control-label">Year：</label>
                <?=
                DatePicker::widget([
                    'name' => 'year',
                    'id' => 'year',
                    'value' => $year,
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'format' => 'yyyy',
                        'multidate' => false,
                        'startView' => 2,    //其实范围（0：日  1：天 2：年）
                        'maxViewMode' => 2,  //最大选择范围（年）
                        'minViewMode' => 2,  //最小选择范围（年）
                    ]
                ]);

                ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>

    <!--    group-->
    <div style="width: 100%;overflow-x: auto">
        <table id="group" class="display" style="margin: 0 !important">
            <thead>
            <tr>
                <th></th>
                <th>January - March</th>
                <th>April - June</th>
                <th>July - September</th>
                <th>October - December</th>
                <th>Option</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,i) in groupData">
                <td>{{item.label}}</td>
                <td v-for="(citem,j) in item.data">
                    <div style="display: flex;align-items: center">
                        <input type="number" maxlength="10" v-model="groupData[i].data[j].value"
                               :disabled="!groupData[i].editStatus">
                        <span style="margin-left: 5px;cursor: pointer"
                              @click="saveGroupOrTeam('group',groupData[i].data[j].id,item.label,groupData[i].data[j].value,groupData[i].data[j].quarter,'')"
                              v-if="groupData[i].editStatus && groupData[i].data[j].value!=groupData[i].data[j].tmpValue">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </span>
                    </div>
                </td>
                <td>
                    <p v-if="groupData[i].field!='reached' && groupData[i].field!='group_target'">
                        <button v-if="!groupData[i].editStatus" type="button" class="btn btn-warning btn-sm"
                                @click="editLine('groupData',i,true)">Edit
                        </button>
                        <button v-else type="button" class="btn btn-info btn-sm" @click="editLine('groupData',i,false)">
                            Cancel
                        </button>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!--    team-->
    <div style="width: 100%;overflow-x: auto">
        <table id="team" class="display" style="margin: 60px 0 !important">
            <thead>
            <tr>
                <th></th>
                <?php foreach (MONTH_MAP as $month): ?>
                    <th><?= $month ?></th>
                <?php endforeach; ?>
                <th>Option</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,i) in teamData">
                <td>{{item.label}}</td>
                <td v-for="(citem,j) in item.data">
                    <div style="display: flex;align-items: center">
                        <input type="number" maxlength="10" style="width: 90px" v-model="teamData[i].data[j].value"
                               :disabled="!teamData[i].editStatus">
                        <span style="margin-left: 5px;cursor: pointer"
                              @click="saveGroupOrTeam('team',citem.id,item.label,citem.value,'',citem.month)"
                              v-if="teamData[i].editStatus && teamData[i].data[j].value!=teamData[i].data[j].tmpValue">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </span>
                    </div>
                </td>
                <td>
                    <p v-if="teamData[i].field!='reached'">
                        <button v-if="!teamData[i].editStatus" type="button" class="btn btn-warning btn-sm"
                                @click="editLine('teamData',i,true)">Edit
                        </button>
                        <button v-else type="button" class="btn btn-info btn-sm" @click="editLine('teamData',i,false)">
                            Cancel
                        </button>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!--    employee-->
    <div style="width: 100%;overflow-x: auto">
        <table id="employee" class="display" style="margin: 0 !important;">
            <thead>
            <tr>
                <th>Month</th>
                <th v-for="employeeName in employeeNames">{{employeeName}}</th>
                <th>Option</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,i) in employeeData">
                <td>{{item.monthLabel}}</td>
                <td v-for="(citem,j) in item.data">
                    <div style="display: flex;align-items: center">
                        <input type="number" maxlength="10" style="width: 90px"
                               :style="'color:'+(employeeData[i].data[j].is_approved==1?'#ff0000':'')"
                               @focus="focus(employeeData[i].data[j].employeeId,item.month,citem.id)"
                               v-model="employeeData[i].data[j].score"
                               :disabled="!employeeData[i].editStatus || employeeData[i].data[j].is_edited==1">
                        <span v-if="item.editStatus && employeeData[i].data[j].is_edited==0"
                              style="margin-left: 5px;cursor: pointer">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </span>
                        <span v-if="!item.editStatus && employeeData[i].data[j].score!=0 &&  employeeData[i].data[j].is_approved==0"
                              style="margin-left: 5px;cursor: pointer">
                        <i @click="approved(employeeData[i].data[j].id)" class="fa fa-angellist" aria-hidden="true"
                           style="color: #0a944c"></i>
                    </span>
                    </div>
                </td>
                <td>
                    <button v-if="!employeeData[i].editStatus" type="button" class="btn btn-warning btn-sm"
                            @click="editLine('employeeData',i,true)">Edit
                    </button>
                    <button v-else type="button" class="btn btn-info btn-sm" @click="editLine('employeeData',i,false)">
                        Cancel
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
            el: "#score-card-container",
            data: {
                groupData: <?= /** @var array $group */ Json::encode($group); ?>,
                teamData: <?= /** @var array $team */ Json::encode($team); ?>,
                employeeData: <?= /** @var array $employee */ Json::encode($employee); ?>,
                employeeNames: <?= /** @var array $employeeNames */ Json::encode($employeeNames); ?>,
                teamId: "<?=$_GET['team_id'] ?? 0?>",
                year: <?= $year ?>
            },
            methods: {
                editLine(dataField, i, status) {
                    var _th = this
                    this[dataField][i].editStatus = status
                    if (!status) {
                        Object.keys(_th[dataField][i].data).map(function (v, j) {
                            _th[dataField][i].data[v].value = _th[dataField][i].data[v].tmpValue
                        })
                    }
                },
                getInitData(){
                    var _th = this
                    $.get('get-data?teamId=' + _th.teamId+'&year='+_th.year, function (res) {
                        _th.groupData = res.group
                        _th.teamData = res.team
                        _th.employeeData = res.employee
                        _th.employeeNames = res.employeeNames
                    })
                },
                approved(id) {
                    $.get('approved?id=' + id, function (res) {
                        layer.msg(res.msg)
                        if (res.status == 1) {
                            window.location.reload()
                        }
                    })
                },
                saveGroupOrTeam(saveType, id, field, value, quarter, month) {
                    var _th = this
                    var data = {
                        id: id,
                        fieldLabel: field,
                        quarter: quarter,
                        month: month,
                        value: value,
                        teamId: this.teamId,
                        year: this.year
                    }
                    $.post("save-group?saveType=" + saveType, data, function (res) {
                        if (res.status == 1) {
                            _th.groupData = res.data.group
                            _th.teamData = res.data.team
                            _th.employeeData = res.data.employee
                            _th.employeeNames = res.data.employeeNames
                            // window.location.reload()
                        }
                        layer.msg(res.msg)
                    }, 'json')
                },
                focus(employeeId, month) {
                    var _th = this
                    layer.closeAll()
                    layer.open({
                        type: 2,
                        title: this.year + '.' + month + ' Edit Score',
                        shadeClose: true,
                        offset: 10,
                        shade: 0.8,
                        area: ['80%', '90%'],
                        end: function () {
                            _th.getInitData(_th.teamId,_th.year)
                        },
                        content: '<?= Url::to('/score-card/edit-score') ?>/?employeeId=' + employeeId + '&year=' + this.year + '&month=' + month
                    });
                }
            }
        })

        $('#group,#team,#employee').DataTable({
            searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
            sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
            bFilter: false,    //去掉搜索框方法三：这种方法可以
            bLengthChange: false,   //去掉每页显示多少条数据方法
            "bInfo": false,
            sort: false,
            paging: false
        });

        $('#year').on('change', function (ev, v) {
            scoreCardVm.year = ev.target.value
        });

        $(".chosen-select").chosen({max_selected_options: 5});

        $("#team-change").change(function (e, v) {
            scoreCardVm.teamId = v.selected
        })
    })
</script>