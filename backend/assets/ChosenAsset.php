<?php

namespace backend\assets;

class ChosenAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/harvesthq/chosen';

    public $css = [
        'chosen.min.css', // ηΆζιη₯
    ];

    public $js = [
        'chosen.jquery.js',
        'chosen.proto.min.js',
    ];
}