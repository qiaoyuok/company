<?php

namespace backend\models;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "{{%service_eymploee_score}}".
 *
 * @property int $id
 * @property int $team_id team ID
 * @property int $employee_id 员工ID
 * @property int $score 分数
 * @property int $is_edited 是否已经编辑过；0：没；1：是
 * @property string $year 年份
 * @property int $month 月份
 * @property int $is_approved 是否已批准
 * @property int $approved_at 批准时间
 * @property int $score_detail 积分详情
 */
class EmployeeScore extends \yii\db\ActiveRecord
{

    const EMPLOYEE_DATA_TEMPLATE = ["id" => 0, 'score' => 0, 'employeeId' => 0, 'is_edited' => 0, 'is_approved' => 0];

    /**
     * 员工的数据模板
     * @return array
     */
    public static function getEmployeeDefaultData()
    {
        $tmpData = [];
        foreach (MONTH_MAP as $month => $monthLabel) {
            $tmpData[] = [
                "month" => $month,
                "monthLabel" => $monthLabel,
                "editStatus" => false,
                'data' => []
            ];
        }

        return $tmpData;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_eymploee_score}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'employee_id', 'score', 'is_edited', 'month', 'is_approved'], 'integer'],
            [['year'], 'required'],
            [['year'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => 'Team ID',
            'employee_id' => 'Employee ID',
            'score' => 'Score',
            'is_edited' => 'Is Edited',
            'year' => 'Year',
            'month' => 'Month',
            'json' => 'Json',
        ];
    }

    public static function getEmployeeData($year, $teamId)
    {
        //1、获取团队的所有成员
        $employeeList = Employee::find()
            ->where(['status' => 1, 'team_id' => $teamId])
            ->asArray()
            ->all();

        $employeeIds = ArrayHelper::getColumn($employeeList, 'id');
        $employeeNames = ArrayHelper::getColumn($employeeList, 'employee');

        $employeeScoreList = self::find()->where(['year' => $year, 'team_id' => $teamId, 'employee_id' => $employeeIds])->select('id,score,employee_id,month,is_edited,is_approved')->asArray()->all();
        $employeeScoreList = ArrayHelper::index($employeeScoreList, 'employee_id', 'month');
        $employeeDefaultData = self::getEmployeeDefaultData();

        foreach ($employeeDefaultData as &$defaultData) {
            foreach ($employeeList as $employee) {
                $dataTemplate = self::EMPLOYEE_DATA_TEMPLATE;
                $dataTemplate['id'] = $employeeScoreList[$defaultData['month']][$employee['id']]['id'] ?? 0;
                $dataTemplate['score'] = isset($employeeScoreList[$defaultData['month']][$employee['id']]['score']) && $defaultData['month'] == $employeeScoreList[$defaultData['month']][$employee['id']]['month'] ? $employeeScoreList[$defaultData['month']][$employee['id']]['score'] : 0;
                $dataTemplate['employeeId'] = $employee['id'];
                $dataTemplate['is_edited'] = $employeeScoreList[$defaultData['month']][$employee['id']]['is_edited'] ?? 0;
                $dataTemplate['is_approved'] = $employeeScoreList[$defaultData['month']][$employee['id']]['is_approved'] ?? 0;
                $defaultData['data'][] = $dataTemplate;
            }
        }


        return [$employeeDefaultData, $employeeNames];
    }
}
