<?php

use yii\helpers\ArrayHelper;

define('FORM_TEMPLATE', "<div class='col-xs-3 col-sm-2 text-right'>{label}</div>
<div class='col-xs-9 col-sm-5'>{input}</div>
<div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>");

define('MONTH_MAP', [
    1 => "January",
    2 => "February",
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "August",
    9 => "September",
    10 => "October",
    11 => "November",
    12 => "December",
]);

/**
 * @param string $paramName
 * @param string $defaultVal
 * @return array|mixed|string
 */
function referrerUri($paramName = '', $defaultVal = '')
{
    $referrer = Yii::$app->request->referrer;
    $urlParse = parse_url($referrer);
    $query = [];
    if (isset($urlParse['query'])) parse_str($urlParse['query'], $query);
    if ($paramName) {
        return $query[$paramName] ?? $defaultVal;
    }

    return $query;
}

/**
 * 转成树形结构
 * @param array $data
 * @param string $id
 * @param string $parentField
 * @param int $parent_id
 * @return array
 */
function toTree($data = [], $id = 'id', $parentField = 'parent_id', $parent_id = 0)
{
    $tmp_arr = [];
    foreach ($data as $k => $v) {
        if ($v[$parentField] == $parent_id) {

            $v['children'] = toTree($data, $id, $parentField, $v[$id]);
            ArrayHelper::multisort($v['children'], ['sort'], [SORT_DESC]);
            $tmp_arr[] = $v;
        }
    }
    ArrayHelper::multisort($tmp_arr, ['sort'], [SORT_DESC]);
    return $tmp_arr;
}