<?php

namespace backend\controllers;

use backend\models\Employee;
use backend\models\EmployeeScore;
use backend\models\RoleScoreCardTemplate;
use Yii;
use backend\models\ScoreCardTemplateDetail;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ScoreCardTemplateDetailController implements the CRUD actions for ScoreCardTemplateDetail model.
 */
class ScoreCardTemplateDetailController extends BaseController
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
     * Lists all ScoreCardTemplateDetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ScoreCardTemplateDetail::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScoreCardTemplateDetail model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ScoreCardTemplateDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ScoreCardTemplateDetail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $templateId
     * @param false $editScore
     * @param int $employeeId
     * @param int $employeeScoreId
     * @param int $year
     * @param int $month
     * @return string
     */
    public function actionConfig($templateId = 0, $editScore = false, $employeeId = 0, $employeeScoreId = 0,$year = 0,$month = 0)
    {

        if ($employeeScoreId){
            $employeeScoreInfo = EmployeeScore::findOne($employeeScoreId);
            if ($employeeScoreInfo){
                $scoreCardTemplateDetail = $employeeScoreInfo->score_detail;
            }
        }

        $templateName = RoleScoreCardTemplate::find()->select('template_name')->where(['score_card_template_id' => $templateId])->scalar();

        if (!isset($scoreCardTemplateDetail)){
            $scoreCardTemplateDetail = ScoreCardTemplateDetail::getDetailTree($templateId);
        }
        $total = 0;
        $totalA = 0;
        foreach ($scoreCardTemplateDetail as $k => $item) {
            $total += intval($item['score']);
            if ($item['total_type'] == 1) {
                $totalA = $total;
            }
        }

        $totalB = intval($total) - intval($totalA);

        return $this->render('config', compact(
            [
                'scoreCardTemplateDetail',
                'templateName',
                'editScore',
                'employeeId',
                'totalA',
                'totalB',
                'total',
                'templateId',
                'employeeScoreId',
                'year',
                'month',
            ]));
    }

    public function actionSaveScore()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $postData = Json::decode(Yii::$app->request->post('data', '[]'), true);
        $employeeId = Yii::$app->request->post('employeeId', 0);
        $templateId = Yii::$app->request->post('templateId', 0);
        $employeeScoreId = Yii::$app->request->post('employeeScoreId', 0);
        $year = Yii::$app->request->post('year',date("Y"));
        $month = Yii::$app->request->post('month',date("m"));

        $employeeScoreInfo = EmployeeScore::findOne($employeeScoreId);

        $employeeInfo = Employee::findOne($employeeId);

        if (empty($templateId)){
            return ['status' => 0, 'msg' => 'param error'];
        }

        if (!$employeeInfo) {
            return ['status' => 0, 'msg' => 'Employee not Exists'];
        }

        if (!$employeeScoreInfo) {
            $employeeScoreInfo = new EmployeeScore();
            $employeeScoreInfo->year = $year;
            $employeeScoreInfo->month = $month;
            $employeeScoreInfo->score_card_template_id = $templateId;
            $employeeScoreInfo->employee_id = $employeeId;
            $employeeScoreInfo->team_id = $employeeInfo->team_id;
        }

        //计算Score
        $total = 0;
        foreach ($postData as $k => &$item) {
            $total += intval($item['score']);
        }

        $employeeScoreInfo->score_detail = $postData;
        $employeeScoreInfo->score = $total;

        if ($employeeScoreInfo->save()) {
            return ['status' => 1, 'msg' => 'save success'];
        } else {
            return ['status' => 0, 'msg' => 'save error'];
        }
    }

    /**
     * Updates an existing ScoreCardTemplateDetail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @return array
     */
    public function actionUpdateDetail()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $postData = Json::decode(Yii::$app->request->post('data', '[]'), true);
            foreach ($postData as $item) {
                ScoreCardTemplateDetail::updateItem($item);
                foreach ($item['children'] as $citem) {
                    ScoreCardTemplateDetail::updateItem($citem);
                }
            }

            return ['status' => 0, 'msg' => '保存成功'];
        } catch (\Exception $exception) {
            return ['status' => 1, 'msg' => $exception->getMessage()];
        }
    }

    /**
     * Deletes an existing ScoreCardTemplateDetail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ScoreCardTemplateDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScoreCardTemplateDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScoreCardTemplateDetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
