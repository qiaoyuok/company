<?php

namespace backend\controllers;

use backend\models\Team;
use common\models\backend\Member;
use Yii;
use backend\models\Employee;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $teamWhere = [];
        $employeeNameWhere = [];


        $teamName = Yii::$app->request->get('team_name', '');
        $employee = Yii::$app->request->get('employee', '');
        $status = Yii::$app->request->get('status', 1);
        $teamId = Yii::$app->request->get('team_id', 0);
        $where = [
            'e.status' => $status,
        ];
        if ($teamName) {
            $teamWhere = ['like', 't.name', $teamName];
        }

        if ($employee) {
            $employeeNameWhere = ['like', 'e.employee', $employee];
        }

        if ($teamId) {
            $where['t.id'] = $teamId;
        }

        $employeeList = Employee::find()
            ->alias('e')
            ->select('e.*,t.name team_name')
            ->orderBy('e.id asc')
            ->where($teamWhere)
            ->andWhere($employeeNameWhere)
            ->andWhere($where)
            ->leftJoin(Team::tableName() . ' as t', 't.id = e.team_id')
            ->asArray()
            ->all();

        return $this->render('index', [
            'employeeList' => $employeeList,
            'status' => $status,
            'teamName' => $teamName,
            'employee' => $employee,
            'teamId' => $teamId,
        ]);
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'status' => referrerUri('status', 1)]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @param int $status
     * @return array|Response
     */
    public function actionStatus($id, $status = 1)
    {
        $db = Yii::$app->db->beginTransaction();
        try {

            if ($id == 1){
                throw new \Exception('No privilege');
            }

            $model = $this->findModel($id);
            $model->status = intval($status);

            //同步后台用户信息
            $memberInfo = Member::findOne($id);
            $statusMap = [
                1=>1,
                2=>0,
            ];
            if($memberInfo){
                $memberInfo->status = $statusMap[$status]??-1;
                if (!$memberInfo->save()){
                    throw new \Exception('Save Error');
                }
            }

            if (!$model->save()) {
                throw new \Exception('Save Error');
            }
            $db->commit();
            return $this->redirect(['index', 'status' => referrerUri('status', 1)]);
        }catch (\Exception $exception){
            $db->rollBack();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status'=>0,'msg'=>$exception->getMessage()];
        }
    }

    /**
     * Deletes an existing Employee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = 3;

        //同步后台用户信息
        $memberInfo = Member::findOne($id);
        if($memberInfo){
            $memberInfo->status = -1;
            if (!$memberInfo->save()){
                throw new \Exception('Save Error');
            }
        }

        $model->save();
        return $this->redirect(['index', 'status' => referrerUri('status', 1)]);
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param int $teamId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetEmployeeList($teamId = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Employee::getEmployeeListByTeamId($teamId);
    }
}
