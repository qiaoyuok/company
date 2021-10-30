<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use common\widgets\adminlet\AdminLetAsset;

/**
 * Class AppAsset
 * @package backend\assets
 * @author jianyan74 <751393839@qq.com>
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/resources';

    public $css = [
        'plugins/toastr/toastr.min.css', // 状态通知
        'plugins/fancybox/jquery.fancybox.min.css', // 图片查看
        'plugins/cropper/cropper.min.css',
        'css/jquery.dataTables.min.css',
        'css/select2.min.css',
        'css/upload.css',
        'css/buttons.dataTables.min.css',
    ];

    public $js = [
        'plugins/layer/layer.js',
        'plugins/sweetalert/sweetalert.min.js',
        'plugins/fancybox/jquery.fancybox.min.js',
        'js/template.js',
        'js/rageframe.js',
        'js/rageframe.widgets.js',
        'js/jquery.dataTables.min.js',
        'js/select2.min.js',
        'js/vue.js',
        'js/buttons.colVis.min.js',
        'js/vfs_fonts.js',
        'js/dataTables.buttons.min.js',
        'js/buttons.html5.min.js',
        'js/pdfmake.min.js',
        'js/jszip.min.js',
        'js/buttons.print.min.js',
        'js/public.js',
    ];

    public $depends = [
        YiiAsset::class,
        AdminLetAsset::class,
        HeadJsAsset::class,
        ChosenAsset::class
    ];
}
