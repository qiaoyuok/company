<?php


namespace backend\controllers;


use backend\models\Employee;
use backend\models\Role;
use common\models\backend\Member;
use common\models\rbac\AuthAssignment;
use common\models\rbac\AuthRole;
use yii\web\Controller;

class UserManagementController extends BaseController
{
    public function actionIndex()
    {
        $members = Member::find()
            ->alias('m')
            ->leftJoin(Employee::tableName().' as e','e.id = m.id')
            ->select('m.username,ar.title,m.id,m.type,e.employee')
            ->leftJoin(AuthAssignment::tableName(). ' as aa','m.id = aa.user_id')
            ->leftJoin(AuthRole::tableName(). ' as ar','ar.id = aa.role_id')
            ->where(['!=','m.status',-1])
            ->asArray()
            ->orderBy('m.id asc')
            ->all();
        return $this->render('index',[
            'members'=>$members
        ]);
    }

    public function actionAdd()
    {

    }
}