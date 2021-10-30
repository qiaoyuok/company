<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_role}}".
 *
 * @property int $id
 * @property string $role_name 角色名
 * @property int $status 状态；1：正常；2：关闭
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_role}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['role_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_name' => 'Role Name',
            'status' => 'Status',
        ];
    }

    /**
     * 获取角色列表
     * @return array
     */
    public static function getRoleList()
    {
        $roleList = self::find()
            ->all();

        $roleListArr = [];

        foreach ($roleList as $role) {
            $roleListArr[$role->id] = $role->role_name;
        }

        return $roleListArr;
    }
}
