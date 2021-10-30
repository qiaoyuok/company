<?php

namespace backend\assets;

class ChosenAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/harvesthq/chosen';

    public $css = [
        'chosen.min.css', // 状态通知
    ];

    public $js = [
        'chosen.jquery.js',
        'chosen.proto.min.js',
    ];
}