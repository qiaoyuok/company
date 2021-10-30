<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_time_sheet_approved}}".
 *
 * @property int $id
 * @property int $employee_id 员工ID
 * @property int $month 月份
 * @property int $is_approved 是否已经通过；0：没有；1：已通过
 * @property int $company_id 公司ID
 * @property int $team_id 团队ＩＤ
 * @property int $to_billing 是否导入Ｂｉｌｌｉｎｇ
 * @property int $time_sheet_id 是否导入Ｂｉｌｌｉｎｇ
 * @property int $billing_id 是否导入Ｂｉｌｌｉｎｇ
 * @property string $year 年份
 * @property string $allowance 津贴
 * @property string $approved_at approved_at的时间
 * @property string $commission 佣金
 */
class TimeSheetApproved extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_time_sheet_approved}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'month', 'is_approved', 'company_id','to_billing','team_id','time_sheet_id','billing_id'], 'integer'],
            [['year'], 'required'],
            [['year', 'approved_at'], 'safe'],
            [['allowance', 'commission'], 'number'],
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
            'month' => 'Month',
            'is_approved' => 'Is Approved',
            'company_id' => 'Company ID',
            'year' => 'Year',
            'allowance' => 'Allowance',
            'approved_at' => 'Approved At',
            'commission' => 'Commission',
        ];
    }
}
