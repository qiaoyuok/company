<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\date\DatePicker;

?>

<div class="box-body" id="time-sheet-container">
    <div class="box-header col-md-12">
        <form action="<?= Url::current() ?>" method="get" class="form-inline">
            <div class="form-group">
                <label class="control-label">Week Ending Date:</label>
                <?=
                DatePicker::widget([
                    'name' => 'date',
                    'id' => 'date-change',
                    'value' => $date ?? date("Y-m-d", strtotime("Saturday")),
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'multidate' => false,
                    ]
                ]); ?>
            </div>
        </form>
    </div>

    <table id="time-sheet" class="display" style="width:100%">
        <thead>
        <th v-for="th in ths">{{th}}</th>
        </thead>
        <tbody>
        <tr v-for="(timeSheet,i) in timeSheets">
            <td width="8%">
                <select class="chosen-select" tabindex="-1"
                        v-model="timeSheets[i].company_id" @change="changeCompany(i)" :disabled="!timeSheets[i].edit">
                    <option value="">Please Select Company</option>
                    <option :value="company.id" v-for="company in companyList">{{company.company_name}}</option>
                </select>
            </td>
            <td width="8%"><input type="number" style="width:100%" v-model="timeSheets[i].day_1.score"
                                  @input="change(i)"
                                  :disabled="!timeSheets[i].edit">
                <input type="number" style="width:100%" v-model="timeSheets[i].day_1.on_site" @input="change(i)"
                       :disabled="!timeSheets[i].edit || !timeSheets[i].on_site_edit">
            </td>
            <td width="8%"><input type="number" style="width:100%" v-model="timeSheets[i].day_2.score"
                                  @input="change(i)"
                                  :disabled="!timeSheets[i].edit">
                <input type="number" style="width:100%" v-model="timeSheets[i].day_2.on_site" @input="change(i)"
                       :disabled="!timeSheets[i].edit || !timeSheets[i].on_site_edit">
            </td>
            <td width="8%"><input type="number" style="width:100%" v-model="timeSheets[i].day_3.score"
                                  @input="change(i)"
                                  :disabled="!timeSheets[i].edit">
                <input type="number" style="width:100%" v-model="timeSheets[i].day_3.on_site" @input="change(i)"
                       :disabled="!timeSheets[i].edit || !timeSheets[i].on_site_edit">
            </td>
            <td width="8%"><input type="number" style="width:100%" v-model="timeSheets[i].day_4.score"
                                  @input="change(i)"
                                  :disabled="!timeSheets[i].edit">
                <input type="number" style="width:100%" v-model="timeSheets[i].day_4.on_site" @input="change(i)"
                       :disabled="!timeSheets[i].edit || !timeSheets[i].on_site_edit">
            </td>
            <td width="8%"><input type="number" style="width:100%" v-model="timeSheets[i].day_5.score"
                                  @input="change(i)"
                                  :disabled="!timeSheets[i].edit">
                <input type="number" style="width:100%" v-model="timeSheets[i].day_5.on_site" @input="change(i)"
                       :disabled="!timeSheets[i].edit || !timeSheets[i].on_site_edit">
            </td>
            <td width="8%"><input type="number" style="width:100%" v-model="timeSheets[i].day_6.score"
                                  @input="change(i)"
                                  :disabled="!timeSheets[i].edit">
                <input type="number" style="width:100%" v-model="timeSheets[i].day_6.on_site" @input="change(i)"
                       :disabled="!timeSheets[i].edit || !timeSheets[i].on_site_edit">
            </td>
            <td width="8%"><input type="number" style="width:100%" v-model="timeSheets[i].day_7.score"
                                  @input="change(i)"
                                  :disabled="!timeSheets[i].edit">
                <input type="number" style="width:100%" v-model="timeSheets[i].day_7.on_site" @input="change(i)"
                       :disabled="!timeSheets[i].edit || !timeSheets[i].on_site_edit">
            </td>
            <td><input type="text" style="width:100%" v-model="timeSheets[i].task_description"
                       :disabled="!timeSheets[i].edit"></td>
            <td width="8%"><input type="text" style="width:100%" v-model="timeSheets[i].total_hours"
                                  disabled="disabled"></td>
            <td width="15%">
                <p v-if="timeSheets[i].status != 1">
                    <button type="button" class="btn btn-danger" @click="deleteRow(i,timeSheets[i].id)">Delete</button>
                    <button type="button" class="btn btn-warning" v-if="!timeSheets[i].edit" @click="editStatus(i)">
                        Edit
                    </button>
                    <button type="button" class="btn btn-warning" v-else @click="editStatus(i)">Cancel</button>
                </p>
            </td>
        </tr>
        </tbody>
    </table>
    <div style="margin: 20px 10px;display: flex;justify-content: space-between">
        <button type="button" class="btn btn-success" @click="addRow">Add Row</button>
        <p>
            <button type="button" class="btn btn-info" @click="save(2)">Save Draft</button>
            <button type="button" class="btn btn-info" @click="save(1)">Submit</button>
        </p>
    </div>
</div>

<script>
    $(document).ready(function () {
        var timeSheetVM = new Vue({
            el: "#time-sheet-container",
            data: {
                date: '',
                ths: [],
                timeSheets: [],
                defaultData: [],
                companyList: [],
            },
            created() {
                this.getData()
            },
            methods: {
                getData() {
                    var _th = this
                    $.get("<?= Url::to('/employee-home/time-sheet-data')?>" + '?date=' + $("#date-change").val(), function (res) {
                        _th.date = res.date
                        _th.ths = res.ths
                        _th.timeSheets = res.timeSheets
                        _th.defaultData = res.defaultData
                        _th.companyList = res.companyList
                        _th.$nextTick(function () {
                            $('#time-sheet').DataTable({
                                searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
                                sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
                                bFilter: false,    //去掉搜索框方法三：这种方法可以
                                bLengthChange: false,   //去掉每页显示多少条数据方法
                                "bInfo": false,
                                paging: false
                            });
                        })
                    })
                },
                changeCompany(i) {
                    if (!this.timeSheets[i].company_id) {
                        this.timeSheets[i].on_site_edit = false
                    } else {
                        this.timeSheets[i].on_site_edit = this.companyList[this.timeSheets[i].company_id].is_on_site == 1
                    }
                },
                addRow() {
                    var tmpData = JSON.parse(JSON.stringify(this.defaultData))
                    this.timeSheets.push(tmpData)
                },
                editStatus(i) {
                    this.timeSheets[i].edit = !this.timeSheets[i].edit
                },
                save(status) {
                    var _th = this
                    var data = {
                        weekEndDate: _th.date,
                        status: status,
                        timeSheets: _th.timeSheets
                    }
                    $.post('/employee-home/save', {data: JSON.stringify(data)}, function (res) {
                        console.log(res, 111);

                        layer.msg(res.msg)

                        if (res.status == 1) {
                            window.location.reload()
                        }
                    })
                },
                change(i) {
                    var _th = this
                    _th.timeSheets[i].total_hours = 0;
                    for (var j = 1; j <= 7; j++) {
                        _th.timeSheets[i].total_hours = parseInt(_th.timeSheets[i].total_hours) + parseInt(_th.timeSheets[i]['day_' + j].score)
                    }
                },
                deleteRow(i, id = 0) {
                    if (confirm("Are you Sure Delete This Row?")) {
                        if (id) {
                            $.get('/employee-home/delete?id=' + id, function (res) {
                                layer.msg(res.msg)
                                if (res.status == 1) {
                                    this.timeSheets.splice(i, 1)
                                }
                            })
                        } else {
                            this.timeSheets.splice(i, 1)
                        }
                    }
                }
            }
        });

        $("#date-change").change(function (e, v) {
            window.location.href = "<?= Url::to('/employee-home/time-sheet')?>?date=" + e.target.value
        })
    })
</script>