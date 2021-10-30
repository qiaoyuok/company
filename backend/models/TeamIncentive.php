<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_team_incentive}}".
 *
 * @property int $id
 * @property int $employee_id
 * @property int $team_id
 * @property string $year
 * @property int $month
 * @property string $target_year
 * @property int $target_month
 * @property string $amount
 * @property int $is_approved
 * @property string $approved_at
 */
class TeamIncentive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_team_incentive}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'team_id', 'month', 'target_month', 'is_approved'], 'integer'],
            [['year', 'target_year', 'approved_at'], 'safe'],
            [['month', 'target_year', 'target_month', 'amount', 'is_approved'], 'required'],
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
            'month' => 'Month',
            'target_year' => 'Target Year',
            'target_month' => 'Target Month',
            'amount' => 'Amount',
            'is_approved' => 'Is Approved',
            'approved_at' => 'Approved At',
        ];
    }
}
