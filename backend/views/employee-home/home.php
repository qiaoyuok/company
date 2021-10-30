<?php


?>

<style>
    .box-header h3 {
        display: flex;
        align-items: center;
    }

    .box-header h3 p.first-label {
        width: 330px;
    }

    .target {
        display: flex;
        margin: 20px 0 !important;
    }

    .target .card {
        border: 1px solid #eeeeee;
        padding: 20px;
        border-radius: 1.5rem;
    }

    .target .card:nth-child(2) {
        margin-left: 30px;
    }

    .target .card .card-title {
        text-align: center;
    }

    /**
 * Inspiration for this project found at:
 * https://markus.oberlehner.net/blog/pure-css-animated-svg-circle-chart
 * 1. The `reverse` animation direction plays the animation backwards
 *    which makes it start at the stroke offset 100 which means displaying
 *    no stroke at all and animating it to the value defined in the SVG
 *    via the inline `stroke-dashoffset` attribute.
 * 2. Rotate by -90 degree to make the starting point of the
 *    stroke the top of the circle.
 * 3. Using CSS transforms on SVG elements is not supported by Internet Explorer
 *    and Edge, use the transform attribute directly on the SVG element as a
 * .  workaround.
 */

    .circle-chart {
        width: 150px;
        height: 150px;
    }

    .circle-chart__circle {
        stroke: #00acc1;
        stroke-width: 2;
        stroke-linecap: square;
        fill: none;
        animation: circle-chart-fill 2s reverse; /* 1 */
        transform: rotate(-90deg); /* 2, 3 */
        transform-origin: center; /* 4 */
    }

    /**
     * 1. Rotate by -90 degree to make the starting point of the
     *    stroke the top of the circle.
     * 2. Scaling mirrors the circle to make the stroke move right
     *    to mark a positive chart value.
     * 3. Using CSS transforms on SVG elements is not supported by Internet Explorer
     *    and Edge, use the transform attribute directly on the SVG element as a
     * .  workaround.
     */

    .circle-chart__circle--negative {
        transform: rotate(-90deg) scale(1, -1); /* 1, 2, 3 */
    }

    .circle-chart__background {
        stroke: #efefef;
        stroke-width: 2;
        fill: none;
    }

    .circle-chart__info {
        animation: circle-chart-appear 2s forwards;
        opacity: 0;
        transform: translateY(0.3em);
    }

    .circle-chart__percent {
        alignment-baseline: central;
        text-anchor: middle;
        font-size: 8px;
    }

    .circle-chart__subline {
        alignment-baseline: central;
        text-anchor: middle;
        font-size: 3px;
    }

    .success-stroke {
        stroke: #00C851;
    }

    .warning-stroke {
        stroke: #ffbb33;
    }

    .danger-stroke {
        stroke: #ff4444;
    }

    @keyframes circle-chart-fill {
        to {
            stroke-dasharray: 0 100;
        }
    }

    @keyframes circle-chart-appear {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .circlechart {
        display: flex;
        justify-content: center;
    }

    .reward-box {
        max-width: 260px;
        height: auto;
        overflow: hidden;
    }
</style>

<div>
    <div class="box-header">
        <h3><p class="first-label">This Month Commission:</p>
            <p>RM<?= number_format($currentMonthCommissionInfo ?? 0, 2) ?></p></h3>
        <h3><p class="first-label">To Receive:</p>
            <p>RM<?= number_format($TotalCommissionInfo ?? 0, 2) ?></p></h3>
    </div>

    <div class="box-body">
        <h3>Score Card</h3>

        <table id="score-card" class="display" style="margin: 0 !important">
            <thead>
            <tr>
                <? foreach (MONTH_MAP as $label): ?>
                    <th><?= $label ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <? /** @var array $scoreCardList */
                foreach ($scoreCardList as $monthK => $item): ?>
                    <td>
                        <?php if (isset($item['score'])): ?>
                            <span data-action="open-score-detail"
                                  data-month="<?= $monthK ?>"><?= $item['score'] ?></span>
                        <?php else: ?>
                            <span>/</span>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            </tbody>
        </table>

        <div class="box-body target row">
            <div class="card col-md-3">
                <div class="card-body">
                    <h4 class="card-title">Group Target</h4>
                    <h5 class="card-title"><?= /** @var string $quarterMonth */
                        $quarterMonth ?></h5>
                    <div class="circlechart" data-percentage="<?= /** @var integer $groupScorePercent */
                    $groupScorePercent ?>"><?= $groupScorePercent ?>%
                    </div>
                </div>
            </div>
            <div class="card col-md-3">
                <div class="card-body">
                    <h4 class="card-title">Team Target</h4>
                    <h5 class="card-title"><?= /** @var integer $month */
                        MONTH_MAP[$month] ?></h5>
                    <div class="circlechart" data-percentage="<?= /** @var integer $teamScorePercent */
                    $teamScorePercent ?>"><?= $teamScorePercent ?>%
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($rewardInfo['is_rewarded']) && $rewardInfo['is_rewarded'] === true): ?>
            <h3>Reward</h3>
            <div class="reward-box col-md-6 col-sm-12">
                <img style="width: 100%;height: auto"
                     src="<?= $rewardInfo['photo'] ?>"
                     alt="">
            </div>

        <?php endif; ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#score-card').DataTable({
            searching: false, //去掉搜索框方法一：百度上的方法，但是我用这没管用
            sDom: '"top"i',   //去掉搜索框方法二：这种方法可以，动态获取数据时会引起错误
            bFilter: false,    //去掉搜索框方法三：这种方法可以
            bLengthChange: false,   //去掉每页显示多少条数据方法
            "bInfo": false,
            sort: false,
            paging: false
        });

        $('.circlechart').circlechart(); // Initialization

        $('[data-action="open-score-detail"]').hover(function () {
            openScoreDetail($(this).data('month'))
        })
    })

    function openScoreDetail(month) {
        layer.closeAll()
        layer.open({
            type: 2,
            title: <?= date("Y") ?>+'.'+month+' Score Detail',
            shadeClose: true,
            offset: 10,
            shade: 0.8,
            area: ['80%', '90%'],
            content: '<?= \yii\helpers\Url::to('/score-card/edit-score') ?>/?onlyShow=1&employeeId=' + <?= Yii::$app->user->getId() ?> + '&year=' + <?= date("Y") ?> + '&month=' + month
    })
        ;
    }

    function makesvg(percentage, inner_text) {
        var inner_text = "";
        var abs_percentage = Math.abs(percentage).toString();
        var percentage_str = percentage.toString();
        var classes = ""

        if (percentage < 0) {
            classes = "danger-stroke circle-chart__circle--negative";
        } else if (percentage > 0 && percentage <= 30) {
            classes = "warning-stroke";
        } else {
            classes = "success-stroke";
        }

        var svg = '<svg class="circle-chart" viewbox="0 0 33.83098862 33.83098862" xmlns="http://www.w3.org/2000/svg">'
            + '<circle class="circle-chart__background" cx="16.9" cy="16.9" r="15.9" />'
            + '<circle class="circle-chart__circle ' + classes + '"'
            + 'stroke-dasharray="' + abs_percentage + ',100"    cx="16.9" cy="16.9" r="15.9" />'
            + '<g class="circle-chart__info">'
            + '   <text class="circle-chart__percent" x="17.9" y="15.5">' + percentage_str + '%</text>';

        if (inner_text) {
            svg += '<text class="circle-chart__subline" x="16.91549431" y="22">' + inner_text + '</text>'
        }

        svg += ' </g></svg>';

        return svg
    }

    (function ($) {

        $.fn.circlechart = function () {
            this.each(function () {
                var percentage = $(this).data("percentage");
                var inner_text = $(this).text();
                $(this).html(makesvg(percentage, inner_text));
            });
            return this;
        };

    }(jQuery));
</script>