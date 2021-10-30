<?php

namespace backend\controllers;

use backend\models\Team;
use Yii;
use backend\models\Company;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use function Amp\all;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends BaseController
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
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex()
    {

        $companyNameWhere = [];
        $companyNumberWhere = [];

        $companyName = Yii::$app->request->get('company_name', '');
        $companyNumber = Yii::$app->request->get('company_number', '');

        if ($companyName) {
            $companyNameWhere = ['like', 'company_name', $companyName];
        }

        if ($companyNumber) {
            $companyNumberWhere = ['like', 'company_number', $companyNumber];
        }

        $companyList = Company::find()
            ->alias('c')
            ->select('c.*,t.name team_name')
            ->orderBy('id asc')
            ->where($companyNameWhere)
            ->andWhere($companyNumberWhere)
            ->andWhere(['c.status' => 1])
            ->leftJoin(Team::tableName() . ' as t', 't.id = c.team_id')
            ->asArray()
            ->all();

        foreach ($companyList as &$item) {
            $item['person_assign'] = Json::decode($item['person_assign'], true);
        }

        return $this->render('index', [
            'companyList' => $companyList
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Company();
        $postData = Yii::$app->request->post();

        if (isset($postData['Company'])) $postData['Company']['created_at'] = time();
        if ($model->load($postData) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * 根据团队ID获取公司
     * @param int $teamId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetCompanyByTeam($teamId = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return empty($teamId)?[]:Company::getCompanyListByTeam($teamId);
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->status = 2;

        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
