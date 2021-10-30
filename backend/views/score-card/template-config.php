<?php

use yii\helpers\Json;
use backend\models\ScoreCardTemplateDetail;

?>
<div class="ranking-index box-body" id="score-card-template">
    <div class="box11-header">
        <h3><?= $rankingName ?? "Default Template" ?></h3>
        <p>
            <button type="button" style="margin-right: 5px" class="btn btn-info" onclick="window.history.back()">Back</button>
            <button type="button" v-if="employeeId==0" class="btn btn-primary" style="float: right"
                    @click="changeEditStatus">
                {{contenteditable ? 'Save' : 'Edit'}}
            </button>
        </p>
    </div>
    <table class="table table-bordered display">
        <tr>
            <th width="50%">Description</th>
            <th width="25%">Score</th>
            <th width="25%">Remarks</th>
        </tr>
        <tbody>

        <tr v-for="(item,i) in scoreCardTemplateDetail">
            <td>
                <input class="item-title" :disabled="!contenteditable"
                       v-model="item.description"
                       :class="item.type==1?'part-title ':' '+[3,4,5].indexOf(item.type)>=0?'float-right':''"/>
                <textarea v-if="(item.type==2 && item.children[0].description!='') || contenteditable"
                          class="item-sub-title"
                          :placeholder="(scoreCardTemplateDetail[i].children[0].description==''&&contenteditable)?'Please Input Description':''"
                          :disabled="!contenteditable"
                          :style="contenteditable?'border:1px solid #eeeeee':''"
                          v-model="scoreCardTemplateDetail[i].children[0].description"></textarea>
            </td>
            <td>
                <input v-if="employeeId!=0 && [3,4,5].indexOf(item.type)<0"
                       style="border: 1px solid #ddd"
                       type="number"
                       max="10"
                       min="1"
                       :disabled="onlyShow==1"
                       @input="inputChange"
                       v-model="scoreCardTemplateDetail[i].score">
                <b v-else>{{scoreCardTemplateDetail[i].score}}</b>
            </td>
            <td>
                <input v-if="employeeId!=0"
                       style="border: 1px solid #ddd"
                       type="text"
                       :disabled="onlyShow==1"
                       placeholder="Please input remarks"
                       v-model="scoreCardTemplateDetail[i].remarks">
            </td>
        </tr>
        </tbody>
    </table>

    <div style="display: flex;align-items: center;justify-content: center;margin: 30px 0" v-if="employeeId !=0 && onlyShow==0">
        <button type="button" class="btn btn-success" @click="saveScore">Create</button>
    </div>
</div>

<script>

    $(document).ready(function () {
        var scoreCardTemplate = new Vue({
            el: "#score-card-template",
            data: {
                contenteditable: false,
                id: <?= $scoreCardTemplateDetail['id'] ?? 0;?>,
                rankingId: <?= $rankingId ?? 0;?>,
                onlyShow: <?= $onlyShow ?? 1;?>,
                scoreCardTemplateDetail: <?= Json::encode($scoreCardTemplateDetail['score_detail']);?>,
                employeeId: "<?= intval($employeeId??0) ?>",
                employeeScoreId: "<?= $scoreCardTemplateDetail['employeeScoreId']??0; ?>",
                year: "<?= intval($year??0) ?>",
                month: "<?= intval($month??0) ?>",
            },
            created() {
                this.$nextTick(function () {
                    $.each($("textarea"), function (i, n) {
                        autoTextarea($(n)[0]);
                    });
                })
            },
            watch: {
                scoreCardTemplateDetail: {
                    handler(newV, oldV) {
                        var _th = this;
                        var subTotal = 0
                        newV.map(function (v, i) {
                            subTotal += parseInt(v.score)
                            if (v.type == 1) {
                                _th.totalA = subTotal
                            } else if (v.type == 2) {
                                subTotalBIndex = i
                            }
                        })
                        _th.total = subTotal;
                        _th.totalB = subTotal - _th.totalA;
                    },
                    deep: true
                }
            },
            methods: {
                changeEditStatus() {
                    var _th = this;
                    _th.contenteditable = !_th.contenteditable
                    if (!_th.contenteditable) {
                        $.post('<?= \yii\helpers\Url::to('/score-card/update') ?>', {
                            id: _th.id,
                            rankingId: _th.rankingId,
                            data: JSON.stringify(_th.scoreCardTemplateDetail)
                        }, function (res) {
                            layer.msg(res.msg)
                            if (res.status === 1) {
                                window.location.href = '<?= \yii\helpers\Url::to("/ranking/index") ?>'
                            }
                        })
                    }
                },
                inputChange(e) {
                    var _th = this
                    var total = 0;
                    var indexA = -1;
                    var indexB = -1;
                    _th.scoreCardTemplateDetail.map(function (v, j) {
                        if ([3, 4, 5].indexOf(v.type) < 0) {
                            total += parseInt(v.score)
                        }

                        if (v.type == 3) {
                            _th.scoreCardTemplateDetail[j].score = total
                            indexA = j
                        }
                        if (v.type == 5) {
                            _th.scoreCardTemplateDetail[j].score = total
                        }

                        if (v.type == 4) {
                            indexB = j
                        }
                    })
                    _th.scoreCardTemplateDetail[indexB].score = total - _th.scoreCardTemplateDetail[indexA].score
                },
                saveScore() {
                    var _th = this
                    $.post('/score-card/save-score', {
                        data: JSON.stringify(_th.scoreCardTemplateDetail),
                        employeeId: _th.employeeId,
                        employeeScoreId: _th.employeeScoreId,
                        year: _th.year,
                        month: _th.month,
                    }, function (res) {
                        layer.msg(res.msg)
                        if (res.status == 1) {
                            setTimeout(() => {
                                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                parent.layer.close(index);
                            }, 500)
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

    .box11-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 60px;
    }

</style>