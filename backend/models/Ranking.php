<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_ranking}}".
 *
 * @property int $id
 * @property string $ranking 等级Lable
 * @property int $commission 佣金占比
 * @property int $status 状态；1：正常；2：删除
 * @property int $role_id 角色ID
 * @property array $score_card 记分卡片
 */
class Ranking extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_ranking}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ranking', 'role_id', 'commission'], 'required'],
            [['commission', 'status', 'role_id'], 'integer'],
            [['score_card'], 'safe'],
            [['ranking'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ranking' => 'Ranking',
            'commission' => 'Commission',
            'status' => 'Status',
            'role_id' => 'Role Id',
        ];
    }

    /**
     * 获取员工等级列表
     * @return array
     */
    public static function getRankingList()
    {

        $rankingList = self::find()->where(['status' => 1])->asArray()->all();

        $rankingListRes = [];

        foreach ($rankingList as $ranking) {
            $rankingListRes[$ranking['id']] = $ranking['ranking'];
        }

        return $rankingListRes;
    }
}
