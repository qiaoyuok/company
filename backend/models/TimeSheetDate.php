<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_time_sheet_date}}".
 *
 * @property int $id
 * @property int $time_sheet_id 时间表ID
 * @property string $date 日期
 * @property int $hour 工时
 * @property int $employee_id 员工ID
 * @property int $approved_id 批准的ID
 * @property int $team_id 批准的ID
 * @property int $is_calc 批准的ID
 * @property int $on_site 打卡次数
 * @property string $year 年份
 * @property int $month 月份
 * @property int $company_id
 */
class TimeSheetDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_time_sheet_date}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['time_sheet_id', 'hour', 'employee_id', 'approved_id', 'on_site', 'month', 'company_id','team_id','is_calc'], 'integer'],
            [['date'], 'required'],
            [['date', 'year'], 'safe'],
            [['time_sheet_id', 'date'], 'unique', 'targetAttribute' => ['time_sheet_id', 'date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_sheet_id' => 'Time Sheet ID',
            'date' => 'Date',
            'hour' => 'Hour',
            'employee_id' => 'Employee ID',
            'approved_id' => 'Approved ID',
            'on_site' => 'On Site',
            'year' => 'Year',
            'month' => 'Month',
            'company_id' => 'Company ID',
        ];
    }
}
