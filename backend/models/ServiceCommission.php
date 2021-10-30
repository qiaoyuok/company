<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_commission}}".
 *
 * @property int $id
 * @property int $company_id
 * @property int $team_id
 * @property string $year
 * @property int $month
 * @property int $employee_id
 * @property int $ranking_id
 * @property string $amount
 * @property int $time_sheet
 * @property int $score
 * @property string $time_sheet_approved_allwance
 * @property string $commission
 */
class ServiceCommission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_commission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'team_id', 'month', 'employee_id', 'ranking_id', 'time_sheet', 'score'], 'integer'],
            [['year'], 'safe'],
            [['amount', 'commission','time_sheet_approved_allwance'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'team_id' => 'Team ID',
            'year' => 'Year',
            'month' => 'Month',
            'employee_id' => 'Employee ID',
            'ranking_id' => 'Ranking ID',
            'amount' => 'Amount',
            'time_sheet' => 'Time Sheet',
            'score' => 'Score',
            'commission' => 'Commission',
        ];
    }
}
