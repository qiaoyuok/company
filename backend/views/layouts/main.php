<?php

use yii\widgets\Breadcrumbs;
use common\helpers\Html;
use backend\assets\AppAsset;
use backend\widgets\Alert;

/* @var $this yii\web\View */

AppAsset::register($this);

?>
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: rgba(0, 0, 0, 0.78) !important;
        }

        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
            padding: 0 !important;
        }
        .dataTable th,.dataTable td{
            text-align: center;
        }
        .nav-tabs li.active a{
            color: #0a73bb !important;
            font-weight: bold;
        }
    </style>
<!--    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.25/b-1.7.1/b-html5-1.7.1/datatables.min.css"/>-->
<!---->
<!--    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>-->
<!--    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>-->
<!--    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.25/b-1.7.1/b-html5-1.7.1/datatables.min.js"></script>-->
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language; ?>">
    <head>
        <meta charset="<?= Yii::$app->charset; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="renderer" content="webkit">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition sidebar-mini fixed">
    <?php $this->beginBody() ?>
    <div class="wrapper-content">
        <section class="content">
            <?= $content; ?>
        </section>
        <?= Alert::widget(); ?>
    </div>
    <!-- 公用底部-->
    <script>
        // 配置
        let config = {
            tag: "<?= Yii::$app->debris->backendConfig('sys_tags') ?? false; ?>",
            isMobile: "<?= Yii::$app->params['isMobile'] ?? false; ?>",
        };
    </script>
    <?= $this->render('_footer') ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>