<?php

use backend\models\Company;
use yii\helpers\Json;
use backend\models\Team;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Billing */
/* @var $form yii\widgets\ActiveForm */


$defaultItems = !empty(Json::decode($billingInfo['billed_amount_detail'],true))  ?
    json_decode($billingInfo['billed_amount_detail'], true) :
    \backend\models\Billing::BILLING_AMOUNT;

?>
<div class="box-header col-md-12">
    <div class="form-inline">
        <div class="form-group">
            <label class="control-label">Team：</label>
            <?= Html::dropDownList('team_id', $billingInfo['team_id'] ?? null, \yii\helpers\ArrayHelper::map($teamList??[], 'id', 'name'), [
                'class' => 'chosen-select',
                'style' => "width:180px",
                'id' => 'team-change',
                'prompt' => 'Please Select Team',
                'disabled'=>$billingInfo['team_id']?'disabled':null
            ]) ?>
        </div>
        <div class="form-group">
            <label class="control-label">Company Name：</label>
            <?= Html::dropDownList('company_id', $billingInfo['company_id'] ?? null, \yii\helpers\ArrayHelper::map($companyList??[], 'id', 'company_name'), [
                'class' => 'chosen-select',
                'style' => "width:180px",
                'id' => 'company-change',
                'prompt' => 'Please Select Company',
                'disabled'=>$billingInfo['company_id']?'disabled':null
            ]) ?>
        </div>
        <div class="form-group">
            <label class="control-label">Invoice Nmber：</label>
            <input name="invoice_number" value="<?= $billingInfo['invoice_number'] ?>" type="text" class="form-control"
                   style="width: 180px;height: 30px" aria-describedby="basic-addon3">
        </div>
        <div class="form-group">
            <label class="control-label">Invoice Date：</label>
            <input name="invoice_date" value="<?= $billingInfo['invoice_date'] ?>" type="date" class="form-control"
                   style="width: 180px;height: 30px" aria-describedby="basic-addon3">
        </div>
    </div>
</div>
<div class="billing-form" id="billing-container">
    <div class="billing-body">
        <table class="display col-md-12" id="create-billing" style="width: 60%" cellspacing="0">
            <thead>
            <tr>
                <th>Items</th>
                <th>Description</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,i) in defaultItems">
                <td>{{defaultItems[i].item}}</td>
                <td><input type="text" v-model="defaultItems[i].description"></td>
                <td><input type="number" v-model="defaultItems[i].price"></td>
            </tr>
            <tr>
                <td></td>
                <td>Total</td>
                <td>{{allAmount}}</td>
            </tr>
            </tbody>
        </table>
        <div style="width: 60%;height: 60px;display: flex;align-items: center;justify-content: center">
            <button type="button" @click="save" class="btn btn-success">Save</button>
            <button type="button" style="margin-left: 5px" class="btn btn-info" onclick="window.history.back()">Back</button>
        </div>
    </div>

</div>

<script>
    $(document).ready(function () {
        var billingVM = new Vue({
            el: "#billing-container",
            data: {
                allAmount: <?=$billingInfo['billed_amount'] ?? 0?>,
                defaultItems: <?= Json::encode($defaultItems) ?>,
            },
            watch: {
                defaultItems: {
                    handler(newValue) {
                        var _th = this;
                        _th.allAmount = 0;
                        newValue.map(function (item,key) {
                            var price = Number(item.price)
                            _th.allAmount = 0 + _th.allAmount + price;
                        })
                    },
                    deep: true
                }
            },
            methods: {
                save() {
                    $.post('<?= \yii\helpers\Url::to('save-amount') ?>', {
                        data: JSON.stringify(this.defaultItems),
                        id: <?= $billingInfo['id'] ?? 0 ?>,
                        teamId: $('#team-change').val(),
                        companyId: $('#company-change').val(),
                        invoice_number: $('input[name="invoice_number"]').val(),
                        invoice_date: $('input[name="invoice_date"]').val(),
                    }, function (res) {
                        layer.msg(res.msg)
                        if (res.status === 1) {
                            window.location.href = '<?= \yii\helpers\Url::to("index") ?>'
                        }
                    })
                }
            }
        });

        $('#create-billing').DataTable({
            searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
            sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
            bFilter: false,    //去掉搜索框方法三：这种方法可以
            bLengthChange: false,   //去掉每页显示多少条数据方法
            "bInfo": false,
            sort: false,
            paging: false
        });

        function getCompanyByTeam(id) {
            $.get("<?= Url::to('/company/get-company-by-team') ?>", {teamId: id}, function (res) {
                refreshSelect(res)
            });
        }

        function refreshSelect(data) {
            $("#company-change").empty();
            $("#company-change").append("<option value=''>Please Select Company</option>")
            data.map(function (item) {
                $("#company-change").append("<option value=" + item.id + ">" + item.company_name + "</option>")
            })

            $("#company-change").trigger('chosen:updated')
        }

        $(".chosen-select").chosen({disable_search_threshold: 10});

        $("#team-change").change(function (e, v) {
            getCompanyByTeam(v.selected)
        })
    })

</script>