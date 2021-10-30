<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_time_sheet}}".
 *
 * @property int $id
 * @property int $employee_id 员工UID
 * @property int $company_id 公司ID
 * @property array $day_1 第1天
 * @property array $day_2 第2天
 * @property array $day_3 第3天
 * @property array $day_4 第4天
 * @property array $day_5 第5天
 * @property array $day_6 第6天
 * @property array $day_7 第7天
 * @property string $task_description 任务描述
 * @property int $total_hours 累计时长
 * @property int $status 1：正常；2：草稿；
 * @property string $week_end_date 结束的日期
 * @property int $total_on_site 累计打卡
 */
class TimeSheet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_time_sheet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'company_id', 'total_hours', 'status', 'total_on_site'], 'integer'],
            [['day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6', 'day_7', 'week_end_date'], 'required'],
            [['day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6', 'day_7', 'week_end_date'], 'safe'],
            [['task_description'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee ID',
            'company_id' => 'Company ID',
            'day_1' => 'Day 1',
            'day_2' => 'Day 2',
            'day_3' => 'Day 3',
            'day_4' => 'Day 4',
            'day_5' => 'Day 5',
            'day_6' => 'Day 6',
            'day_7' => 'Day 7',
            'task_description' => 'Task Description',
            'total_hours' => 'Total Hours',
            'status' => 'Status',
            'week_end_date' => 'Week End Date',
            'total_on_site' => 'Total On Site',
        ];
    }
}
