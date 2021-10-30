<?php

namespace backend\controllers;

use app\models\ScoreCardTemplate;
use common\models\rbac\AuthRole;
use Yii;
use backend\models\Ranking;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RankingController implements the CRUD actions for Ranking model.
 */
class RankingController extends BaseController
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
     * Lists all Ranking models.
     * @return mixed
     */
    public function actionIndex()
    {
        $rankingList = Ranking::find()
            ->alias('r')
            ->leftJoin(AuthRole::tableName() . ' as ar', 'ar.id = r.role_id')
            ->leftJoin(ScoreCardTemplate::tableName() . ' as sct', 'sct.ranking_id = r.id')
            ->select("r.*,sct.id sctId,ar.title")
            ->where(['r.status' => 1])
            ->asArray()
            ->all();

        return $this->render('index', [
            'rankingList' => $rankingList,
        ]);
    }

    /**
     * Creates a new Ranking model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ranking();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            if (Yii::$app->request->isPost) {
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Ranking model.
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
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Ranking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionConfig($id)
    {
        $scoreCardTemplateDetailTmp = ScoreCardTemplate::find()
            ->where(['ranking_id' => $id])
            ->orderBy('part_no asc,sort desc')
            ->asArray()
            ->all();

        $scoreCardTemplateDetailGroup = ArrayHelper::index($scoreCardTemplateDetailTmp, null, 'part_no');
        $scoreCardTemplateDetail = [];
        foreach ($scoreCardTemplateDetailGroup as $j => $group) {
            foreach ($group as $k => &$item) {
                foreach (explode("\n", $item['description']) as $n => $desc) {
                    preg_match("/^\s+-.+/", $desc, $res);
                    $item['descriptionArr'][$n]['text'] = $desc;
                    $item['descriptionArr'][$n]['isTitle'] = $k === 0;
                    $item['descriptionArr'][$n]['isSubTitle'] = !boolval($res);
                    $item['isCanEdit'] = true;
                }
                $scoreCardTemplateDetail[] = $item;
            }
            $scoreCardTemplateDetail[] = [
                'descriptionArr' => [
                    ['text' => 'Total ' . ($j == 1 ? 'A' : 'B'), 'isTitle' => false, 'isSubTitle' => false],
                ],
                'isCanEdit' => false
            ];
        }

        $scoreCardTemplateDetail[] = [
            'descriptionArr' => [
                ['text' => 'Total A+B', 'isTitle' => false, 'isSubTitle' => false],
            ],
            'isCanEdit' => false
        ];

        return $this->render('/score-card-template/config', [
            'scoreCardTemplateDetail' => $scoreCardTemplateDetail
        ]);
    }

    /**
     * Deletes an existing Ranking model.
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
     * Finds the Ranking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ranking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ranking::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
