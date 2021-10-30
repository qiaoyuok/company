<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%service_group_score_card}}".
 *
 * @property int $id
 * @property int $team_id teamID
 * @property string $group_target group目标
 * @property string $Incentive group 激励金
 * @property string $reached 已达到金额
 * @property string $year 年份
 * @property int $quarter 季度
 */
class GroupScoreCard extends \yii\db\ActiveRecord
{

    const GROUP_FILED_LABEL_MAP = [
        'Group Target' => "group_target",
        'Group Incentive' => "Incentive",
        'Reached' => "reached",
    ];

    //季度对应月份
    const QUARTER_RANGE_MONTH = [
        1 => [1, 2, 3],
        2 => [4, 5, 6],
        3 => [7, 8, 9],
        4 => [10, 11, 12],
    ];

    /**
     * 获取group defaultData
     * @return array[]
     */
    public static function getGroupDefaultData()
    {
        $tmpData = [];
        for ($i = 1; $i <= 4; $i++) {
            $tmpData[] = ['id' => 0, 'value' => 0, 'tmpValue' => 0, 'quarter' => $i];
        }

        return [
            [
                'label' => "Group Target",
                'editStatus' => false,
                'field' => "group_target",
                "data" => $tmpData
            ],
            [
                'label' => "Group Incentive",
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
        return '{{%service_group_score_card}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'quarter'], 'integer'],
            [['group_target', 'Incentive', 'reached'], 'number'],
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
            'group_target' => 'Group Target',
            'Incentive' => 'Incentive',
            'reached' => 'Reached',
            'year' => 'Year',
            'quarter' => 'Quarter',
        ];
    }

    /**
     * 获取Group Data
     * @param $year
     * @param $teamId
     * @return array[]
     */
    public static function getGroupData($year, $teamId)
    {

        $data = self::find()
            ->where(['year' => $year, 'team_id' => $teamId])
            ->asArray()
            ->all();

        $data = ArrayHelper::index($data, 'quarter');
        $resData = self::getGroupDefaultData();

        foreach ($resData as $key => &$item) {
            foreach ($item['data'] as &$itemData) {
                $itemData['id'] = $data[$itemData['quarter']]['id'] ?? 0;
                $itemData['value'] = $data[$itemData['quarter']][$item['field']] ?? 0;
                $itemData['tmpValue'] = $data[$itemData['quarter']][$item['field']] ?? 0;
            }
        }

        return $resData;
    }

    /**
     * 保存绩效
     * @param $teamId
     * @param $reached
     * @param $year
     * @param $month
     * @throws \Exception
     */
    public static function saveReached($teamId, $reached, $year, $month)
    {

        foreach (self::QUARTER_RANGE_MONTH as $quarter => $months) {
            if (in_array($month, $months)) break;
        }

        $info = self::findOne(['team_id' => $teamId, 'year' => $year, 'quarter' => $quarter]);
        if (!$info) {
            $info = new self();
            $info->year = $year;
            $info->team_id = $teamId;
            $info->quarter = $quarter;
        }
        $info->reached += $reached;
        if ($info->save()) {
            $teamReached = TeamScoreCard::findOne(['team_id' => $teamId, 'year' => $year, 'month' => $month]);
            if (!$teamReached) {
                $teamReached = new TeamScoreCard();
                $teamReached->year = $year;
                $teamReached->month = $month;
                $teamReached->team_id = $teamId;
            }
            $teamReached->reached += $reached;
            if (!$teamReached->save()) {
                throw new \Exception(Json::encode($teamReached));
            }
        } else {
            throw new \Exception(Json::encode($info->getErrors()));
        }
    }
}
