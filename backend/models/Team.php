<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_team}}".
 *
 * @property int $id
 * @property string $name 团队名称
 * @property int $created_at 添加时间
 * @property int $status 状态；1：正常；2：删除
 * @property string $updated_at 更新时间
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_team}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'status'], 'integer'],
            [['updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    /**
     * 获取团队列表
     * @param int $companyId
     * @return array
     */
    public static function getTeamList($companyId = 0)
    {

        $where = [];

        if ($companyId) {
            $where['company_id'] = $companyId;
        }

        return self::find()->where($where)->andWhere(['status' => 1])->asArray()->all();

    }

    /**
     * 获取团队列表
     * @param int $companyId
     * @return array
     */
    public static function getTeamListGroupCompany()
    {

        $teamList = self::find()->asArray()->all();

        $teamListRes = [];

        foreach ($teamList as $team) {
            $teamListRes[$team['company_id']][$team['id']] = $team['name'];
        }

        return $teamListRes;
    }
}
