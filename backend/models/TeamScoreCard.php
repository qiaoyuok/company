<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%service_team_score_card}}".
 *
 * @property int $id
 * @property int $team_id teamID
 * @property string $team_target team目标；group/3得到
 * @property string $Incentive team 激励金
 * @property string $reached 已达到金额
 * @property string $year 年份
 * @property int $month 月份
 */
class TeamScoreCard extends \yii\db\ActiveRecord
{
    const TEAM_FILED_LABEL_MAP = [
        'Team Target' => "team_target",
        'Team Incentive' => "Incentive",
        'Reached' => "reached",
    ];

    /**
     * 获取team defaultData
     * @return array
     */
    public static function getTeamDefaultData()
    {
        $tmpData = [];
        for ($i = 1; $i <= 12; $i++) {
            $tmpData[] = ['id' => 0, 'value' => 0, 'tmpValue' => 0,'month'=>$i];
        }

        return [
            [
                'label' => "Team Target",
                'editStatus' => false,
                'field' => "team_target",
                "data" => $tmpData
            ],
            [
                'label' => "Team Incentive",
                'editStatus' => false,
                'field' => "Incentive",
                "data" => $tmpData
            ],
            [
                'label' => "Reached",
                'editStatus' => false,
                'field' => "reached",
                "data" => $tmpData
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_team_score_card}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'month'], 'integer'],
            [['team_target', 'Incentive', 'reached'], 'number'],
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
            'team_target' => 'Team Target',
            'Incentive' => 'Incentive',
            'reached' => 'Reached',
            'year' => 'Year',
            'month' => 'Month',
        ];
    }

    /**
     * Team Data
     * @param $year
     * @param $teamId
     * @return array[]
     */
    public static function getTeamData($year, $teamId)
    {

        $data = self::find()
            ->where(['year' => $year, 'team_id' => $teamId])
            ->asArray()
            ->all();

        $data = ArrayHelper::index($data, 'month');
        $resData = self::getTeamDefaultData();

        foreach ($resData as $key => &$item) {
            foreach ($item['data'] as &$itemData) {
                $itemData['id'] = $data[$itemData['month']]['id'] ?? 0;
                $itemData['value'] = $data[$itemData['month']][$item['field']] ?? 0;
                $itemData['tmpValue'] = $data[$itemData['month']][$item['field']] ?? 0;
            }
        }

        return $resData;
    }

    /**
     * 保存数据
     * @param $year
     * @param $teamId
     * @param $month
     * @param $field
     * @param $value
     * @throws \Exception
     */
    public static function saveRecord($year,$teamId,$month,$field,$value)
    {
        $scoreInfo = self::findOne(['year' => $year, 'team_id' => $teamId, 'month' => $month]);
        if (!$scoreInfo) {
            $scoreInfo = new TeamScoreCard();
            $scoreInfo->year = $year;
            $scoreInfo->team_id = $teamId;
            $scoreInfo->month = $month;
        }

        $scoreInfo->$field = $value;

        if (!$scoreInfo->save()){
            throw new \Exception("error");
        }
    }
}
