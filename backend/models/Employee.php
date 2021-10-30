<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_employee}}".
 *
 * @property int $id
 * @property string $employee 员工名字
 * @property int $team_id 团队ID
 * @property int $ranking_id 等级排名ID
 * @property int $status 状态
 * @property string $joined_at 加入时间
 * @property string $basic_salary 基础薪资
 * @property string $allowance 津贴
 * @property string $account 后台账号
 * @property int $commission_ccumulative 累计佣金
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_employee}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee'], 'required'],
            [['team_id', 'ranking_id', 'status'], 'integer'],
            [['joined_at'], 'safe'],
            [['basic_salary', 'allowance','commission_ccumulative'], 'number'],
            [['employee','account'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee' => 'Employee',
            'team_id' => 'Team ID',
            'ranking_id' => 'Ranking ID',
            'joined_at' => 'Joined At',
            'basic_salary' => 'Basic Salary',
            'allowance' => 'Allowance',
            'account' => 'Account',
            'status' => 'Status',
            'commission_ccumulative' => 'commission_ccumulative',
        ];
    }

    /**
     * @param int $companyId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEmployeeList($companyId = 0)
    {
        $where = [];
        if ($companyId) {
            $where['company_id'] = $companyId;
        }

        return self::find()->where($where)->asArray()->all();
    }

    /**
     * @param int $teamId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEmployeeListByTeamId($teamId = 0)
    {
        $where = [];
        if ($teamId) {
            $where['team_id'] = $teamId;
        }

        return self::find()->where($where)->asArray()->all();
    }
}
