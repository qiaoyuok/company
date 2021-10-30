<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_role_score_card_template}}".
 *
 * @property int $score_card_template_id 积分卡模板ID
 * @property int $role_id 角色ID
 * @property string $template_name 模板名
 * @property int $status 状态；1：正常；2：关闭
 * @property string $updated_at 上次更新时间
 */
class RoleScoreCardTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_role_score_card_template}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'template_name'], 'required'],
            [['role_id', 'status'], 'integer'],
            [['updated_at'], 'safe'],
            [['template_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'score_card_template_id' => 'Score Card Template ID',
            'role_id' => 'Role ID',
            'template_name' => 'Template Name',
            'status' => 'Status',
            'updated_at' => 'Updated At',
        ];
    }
}
