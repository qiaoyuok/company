<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_group_incentive}}".
 *
 * @property int $id
 * @property int $employee_id
 * @property int $team_id
 * @property string $year
 * @property int $quarter
 * @property string $target_year
 * @property int $target_month
 * @property string $amount
 * @property int $is_approved
 * @property string $approved_at
 */
class GroupIncentive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_group_incentive}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'team_id', 'quarter', 'target_month', 'is_approved'], 'integer'],
            [['year', 'target_year', 'approved_at'], 'safe'],
            [['quarter', 'target_year', 'target_month', 'amount', 'is_approved'], 'required'],
            [['amount'], 'number'],
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
            'team_id' => 'Team ID',
            'year' => 'Year',
            'quarter' => 'Quarter',
            'target_year' => 'Target Year',
            'target_month' => 'Target Quarter',
            'amount' => 'Amount',
            'is_approved' => 'Is Approved',
            'approved_at' => 'Approved At',
        ];
    }
}
