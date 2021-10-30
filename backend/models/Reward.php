<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_reward}}".
 *
 * @property int $id
 * @property string $date 日期
 * @property string $photo 图片地址
 * @property int $Type 奖励类型；1：Cash；2：No-Cash
 * @property string $reward_name 奖励的名称
 * @property array $member 奖励的成员
 * @property int $team_id 团队ID
 */
class Reward extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_reward}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'reward_name', 'member'], 'required'],
            [['date', 'member'], 'safe'],
            [['Type', 'team_id'], 'integer'],
            [['photo', 'reward_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'photo' => 'Photo',
            'Type' => 'Type',
            'reward_name' => 'Reward Name',
            'member' => 'Member',
            'team_id' => 'Team ID',
        ];
    }
}
