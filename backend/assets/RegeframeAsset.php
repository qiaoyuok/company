<?php

namespace backend\assets;

class RegeframeAsset extends \yii\web\AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/resources';

    public $css = [
        'css/rageframe.css',
        'css/rageframe.widgets.css',
    ];

    public $depends = [
        AppAsset::class
    ];
}