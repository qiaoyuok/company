<?php

use yii\helpers\Json;
use backend\models\ScoreCardTemplateDetail;

?>

<style>
    .part-title {
        color: #42a7ed;
    }

    .item-title {
        font-size: 15px;
        font-weight: bold;
    }

    .item-sub-title {
        font-size: 14px;
        text-indent: 2rem;
    }

    .desc-p {
        margin: 0;
    }

    .score-card-description {
        padding: 15px 6px !important;
    }

    textarea, input {
        border: 0;
        width: 100%;
        overflow: hidden;
        background: none;
        resize: none !important;
    }

    .float-right {
        text-align: right;
    }

    textarea::placeholder {
        color: #ec9039;
    }

    .box11-header{
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 60px;
    }

</style>
<div class="ranking-index box-body" id="score-card-template">
    <div class="box11-header">
        <h3><?= $templateName ?? "Default Template" ?></h3>
        <button type="button" v-if="!editScore" class="btn btn-primary" style="float: right" @click="changeEditStatus">
            {{contenteditable ? 'Save' : 'Edit'}}
        </button>
    </div>
    <table class="table table-bordered display">
        <tr>
            <th width="50%">Description</th>
            <th width="25%">Score (1/10)</th>
            <th width="25%">Remarks</th>
        </tr>
        <tbody>

        <tr v-for="(item,i) in scoreCardTemplateDetail">
            <td>
                <input class="item-title" :disabled="!contenteditable"
                       v-model="item.description"
                       :class="item.is_part_title==1?'part-title ':' '+item.total_type!=0?'float-right':''"/>
                <textarea v-if="item.total_type==0 && item.is_part_title==2" class="item-sub-title"
                          :placeholder="(scoreCardTemplateDetail[i].children[0].description==''&&contenteditable)?'Please Input Description':''"
                          :disabled="!contenteditable"
                          :style="contenteditable?'border:1px solid #eeeeee':''"
                          v-model="scoreCardTemplateDetail[i].children[0].description"></textarea>
            </td>
            <td>
                <input v-if="editScore && item.total_type==0"
                       style="border: 1px solid #ddd"
                       type="number"
                       max="10"
                       min="1"
                       v-model="scoreCardTemplateDetail[i].score">
                <b v-else-if="item.total_type == 1">{{totalA}}</b>
                <b v-else-if="item.total_type == 2">{{totalB}}</b>
                <b v-else-if="item.total_type == 3">{{total}}</b>
            </td>
            <td>
                <input v-if="editScore && item.total_type==0"
                       style="border: 1px solid #ddd"
                       type="text"
                       placeholder="Please input remarks"
                       v-model="scoreCardTemplateDetail[i].remarks">
            </td>
        </tr>
        </tbody>
    </table>

    <div style="display: flex;align-items: center;justify-content: center;margin: 30px 0">
        <button type="button" class="btn btn-success" @click="saveScore">Create</button>
    </div>
</div>

<script>

    $(document).ready(function () {
        var scoreCardTemplate = new Vue({
            el: "#score-card-template",
            data: {
                contenteditable: false,
                scoreCardTemplateDetail: <?= Json::encode($scoreCardTemplateDetail);?>,
                editScore:"<?= boolval($editScore) ?>",
                employeeId:"<?= intval($employeeId) ?>",
                totalA:"<?= intval($totalA) ?>",
                totalB:"<?= intval($totalB) ?>",
                total:"<?= intval($total) ?>",
                employeeScoreId:"<?= intval($employeeScoreId) ?>",
                templateId:"<?= intval($templateId) ?>",
                year:"<?= intval($year) ?>",
                month:"<?= intval($month) ?>",
            },
            created() {
                this.$nextTick(function () {
                    $.each($("textarea"), function (i, n) {
                        autoTextarea($(n)[0]);
                    });
                })
            },
            watch:{
                scoreCardTemplateDetail:{
                    handler(newV,oldV){
                        var _th = this;
                        var subTotal = 0
                        newV.map(function (v,i){
                            subTotal +=parseInt(v.score)
                            if (v.total_type == 1){
                                _th.totalA = subTotal
                            }else if(v.total_type == 2){
                                subTotalBIndex = i
                            }
                        })
                        _th.total = subTotal;
                        _th.totalB = subTotal-_th.totalA;
                    },
                    deep:true
                }
            },
            methods: {
                changeEditStatus() {
                    this.contenteditable = !this.contenteditable
                    if (!this.contenteditable) {
                        $.post('<?= \yii\helpers\Url::to('update-detail') ?>', {data: JSON.stringify(this.scoreCardTemplateDetail)}, function (res) {
                            console.log(res, 998);
                            if (res.status === 0) {
                                window.location.href = '<?= \yii\helpers\Url::to(["config", "score_card_template_id" => $_GET["score_card_template_id"] ?? 0]) ?>'
                            }
                        })
                    }
                },
                saveScore(){
                    var _th = this
                    $.post('/score-card-template-detail/save-score', {
                        data:JSON.stringify(_th.scoreCardTemplateDetail),
                        employeeId:_th.employeeId,
                        employeeScoreId:_th.employeeScoreId,
                        templateId:_th.templateId,
                        year:_th.year,
                        month:_th.month,
                    },function (res) {
                        layer.msg(res.msg)
                        if (res.status == 1){
                            setTimeout(()=>{
                                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                parent.layer.close(index);
                            },500)
                        }
                    })
                }
            }
        });
    })

    /**
     * 文本框根据输入内容自适应高度
     * {HTMLElement}   输入框元素
     * {Number}        设置光标与输入框保持的距离(默认0)
     * {Number}        设置最大高度(可选)
     */
    var autoTextarea = function (elem, extra, maxHeight) {
        extra = extra || 5;
        var isFirefox = !!document.getBoxObjectFor || 'mozInnerScreenX' in window,
            isOpera = !!window.opera && !!window.opera.toString().indexOf('Opera'),
            addEvent = function (type, callback) {
                elem.addEventListener ?
                    elem.addEventListener(type, callback, false) :
                    elem.attachEvent('on' + type, callback);
            },
            getStyle = elem.currentStyle ?
                function (name) {
                    var val = elem.currentStyle[name];
                    if (name === 'height' && val.search(/px/i) !== 1) {
                        var rect = elem.getBoundingClientRect();
                        return rect.bottom - rect.top -
                            parseFloat(getStyle('paddingTop')) -
                            parseFloat(getStyle('paddingBottom')) + 'px';
                    }
                    ;
                    return val;
                } : function (name) {
                    return getComputedStyle(elem, null)[name];
                },
            minHeight = parseFloat(getStyle('height'));
        elem.style.resize = 'both';//如果不希望使用者可以自由的伸展textarea的高宽可以设置其他值

        var change = function () {
            var scrollTop, height,
                padding = 10,
                style = elem.style;

            if (elem._length === elem.value.length) return;
            elem._length = elem.value.length;

            if (!isFirefox && !isOpera) {
                padding = parseInt(getStyle('paddingTop')) + parseInt(getStyle('paddingBottom'));
            }
            ;
            scrollTop = document.body.scrollTop || document.documentElement.scrollTop;

            elem.style.height = minHeight + 'px';
            if (elem.scrollHeight > minHeight) {
                if (maxHeight && elem.scrollHeight > maxHeight) {
                    height = maxHeight - padding;
                    style.overflowY = 'auto';
                } else {
                    height = elem.scrollHeight - padding;
                    style.overflowY = 'hidden';
                }
                ;
                style.height = height + extra + 'px';
                scrollTop += parseInt(style.height) - elem.currHeight;
                document.body.scrollTop = scrollTop;
                document.documentElement.scrollTop = scrollTop;
                elem.currHeight = parseInt(style.height);
            }
            ;
        };

        addEvent('propertychange', change);
        addEvent('input', change);
        addEvent('focus', change);
        change();
    };
</script>
