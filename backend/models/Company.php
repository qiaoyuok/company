<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%service_company}}".
 *
 * @property int $id
 * @property string $company_name 公司名称
 * @property string $company_pic 公司图片？？？
 * @property string $company_number 公司联系电话
 * @property int $is_on_site 是否在线
 * @property int $company_id 团队ID
 * @property array $person_assign 指派的员工
 * @property int $created_at 添加时间
 * @property int $status 状态；1：正常；2：删除
 * @property string $updated_at 上次更新时间
 * @property string $company_code 上次更新时间
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_company}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_name', 'company_pic', 'company_number', 'is_on_site', 'person_assign'], 'required'],
            [['is_on_site', 'team_id', 'created_at', 'status'], 'integer'],
            [['person_assign', 'updated_at'], 'safe'],
            [['company_name', 'company_pic', 'company_number', 'company_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_name' => 'Company Name',
            'company_pic' => 'Company Pic',
            'company_number' => 'Contact Number',
            'is_on_site' => 'Is On Site',
            'team_id' => 'Team ID',
            'person_assign' => 'Person Assign',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'company_code' => 'Company Code',
        ];
    }

    /**
     * 获取公司列表
     * @return array
     */
    public static function getCompanyList()
    {

        return self::find()->asArray()->all();
    }

    public static function getCompanyListByTeam($teamId)
    {
        return self::find()->asArray()->where(['team_id'=>$teamId,'status'=>1])->all();
    }

    /**
     * 获取员工被指派的公司
     * @param $employeeId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCompanyListByEmployee($employeeId)
    {
        $allCompanyList = self::find()
            ->alias('c')
            ->leftJoin(Team::tableName() . ' as t', 'c.team_id = t.id')
            ->leftJoin(Employee::tableName() . ' e', 'e.team_id = t.id')
            ->where(['e.id' => $employeeId])
            ->select('c.id,c.person_assign,company_name,is_on_site')
            ->asArray()
            ->all();

        foreach ($allCompanyList as $k => &$company) {
            $company['person_assign'] = Json::decode($company['person_assign'], true);
            if (!in_array($employeeId, $company['person_assign'])) {
                unset($allCompanyList[$k]);
            }
        }

        return ArrayHelper::index($allCompanyList, 'id');
    }
}
