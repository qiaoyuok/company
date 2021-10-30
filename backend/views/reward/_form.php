<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Employee;
use yii\helpers\ArrayHelper;
use backend\models\Team;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Reward */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="reward-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => FORM_TEMPLATE,
        ]]); ?>
    <?= $form->field($model, 'reward_name')->textInput(['maxlength' => true]) ?>

    <input type="text" name="Reward[date]" value="<?= $model->date ?>" hidden>

    <?= $form->field($model, 'photo')->widget(\common\widgets\webuploader\Files::class, [
        'type' => 'images',
        'theme' => 'default',
        'themeConfig' => [],
        'config' => [
            // 可设置自己的上传地址, 不设置则默认地址
             'server' => '',
            'pick' => [
                'multiple' => false,
            ],
        ]
    ]); ?>

    <?= $form->field($model, 'Type')->dropDownList([1 => 'Cash', 2 => "No Cash"],
        ['class' => 'chosen-select', 'style' => "width:280px"]) ?>

    <?= $form->field($model, 'team_id')->dropDownList(ArrayHelper::map(Team::getTeamList(), 'id', 'name'),
        ['class' => 'chosen-select team-change', 'style' => "width:280px", 'prompt' => 'Please Select Team']) ?>

    <?= $form->field($model, 'member')->dropDownList($model->team_id ? ArrayHelper::map(Employee::getEmployeeListByTeamId($model->team_id), 'id', 'employee') : [],
        ['class' => 'chosen-select team-select','multiple'=>'multiple', 'style' => "width:280px", 'prompt' => 'Please Select Member',]) ?>

    <div class="form-group">
        <div class="form-group">
            <div class='col-xs-3 col-sm-2 text-right'></div>
            <div class='col-xs-9 col-sm-7'>
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <button type="button" style="margin-left: 5px" class="btn btn-info" onclick="window.history.back()">Back</button>
            </div>
            <div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'></div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {
        $(".chosen-select").chosen({disable_search_threshold: 10});

        $(".team-change").change(function (e, v) {
            console.log(e, v, 111);
            getContractListByTid(v.selected)
        })

        function getContractListByTid(id) {
            $.get("<?= Url::to('/employee/get-employee-list') ?>", {teamId: id}, function (res) {
                refreshSelect(res)
            });
        }

        function refreshSelect(data) {
            $(".team-select").empty();
            data.map(function (item) {
                $(".team-select").append("<option value=" + item.id + ">" + item.employee + "</option>")
            })

            $(".team-select").trigger('chosen:updated')
        }
    })
</script>
