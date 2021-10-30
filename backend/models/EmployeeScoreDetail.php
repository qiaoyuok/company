<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_employee_score_detail}}".
 *
 * @property int $id
 * @property int $score_card_template_detail_id 积分模板详情ID
 * @property int $created_at 添加时间
 * @property int $score 分数
 * @property string $remarks 备注
 */
class EmployeeScoreDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_employee_score_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['score_card_template_detail_id', 'created_at', 'score'], 'integer'],
            [['remarks'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'score_card_template_detail_id' => 'Score Card Template Detail ID',
            'created_at' => 'Created At',
            'score' => 'Score',
            'remarks' => 'Remarks',
        ];
    }
}
