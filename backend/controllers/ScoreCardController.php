<?php


namespace backend\controllers;


use app\models\ScoreCardTemplate;
use backend\models\Employee;
use backend\models\EmployeeScore;
use backend\models\GroupScoreCard;
use backend\models\Ranking;
use backend\models\Team;
use backend\models\TeamScoreCard;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

class ScoreCardController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $getData = \Yii::$app->request->get();
        $teamId = ArrayHelper::getValue($getData, 'team_id', 0);
        $year = ArrayHelper::getValue($getData, 'year', date("Y"));

        $data['group'] = GroupScoreCard::getGroupData($year, $teamId);
        $data['team'] = TeamScoreCard::getTeamData($year, $teamId);
        list($data['employee'], $data['employeeNames']) = EmployeeScore::getEmployeeData($year, empty($teamId) ? -1 : $teamId);
        return $this->render('index', $data);
    }

    /**
     * 获取数据
     * @param $teamId
     * @param $year
     * @return mixed
     */
    public static function getData($teamId, $year)
    {
        $data['group'] = GroupScoreCard::getGroupData($year, $teamId);
        $data['team'] = TeamScoreCard::getTeamData($year, $teamId);
        list($data['employee'], $data['employeeNames']) = EmployeeScore::getEmployeeData($year, empty($teamId) ? -1 : $teamId);
        return $data;
    }

    /**
     * @param $teamId
     * @param $year
     * @return mixed
     */
    public function actionGetData($teamId, $year)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return self::getData($teamId, $year);
    }

    /**
     * 保存Group数据
     * @param string $saveType
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionSaveGroup($saveType = '')
    {
        try {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $postData = \Yii::$app->request->post();
            $id = ArrayHelper::getValue($postData, 'id', 0);
            $year = ArrayHelper::getValue($postData, 'year', date("Y"));
            $teamId = ArrayHelper::getValue($postData, 'teamId', 0);
            $quarter = ArrayHelper::getValue($postData, 'quarter', 1);
            $month = ArrayHelper::getValue($postData, 'month', 1);
            $value = ArrayHelper::getValue($postData, 'value', 0);

            $db = \Yii::$app->db->beginTransaction();
            if (!Team::findOne($teamId)) return ['status' => 0, 'msg' => 'Team not exists'];

            if ($saveType == 'group') {
                if (!array_key_exists($postData['fieldLabel'], GroupScoreCard::GROUP_FILED_LABEL_MAP)) return ['status' => 0, 'msg' => 'error'];

                $field = GroupScoreCard::GROUP_FILED_LABEL_MAP[$postData['fieldLabel']];

                $scoreInfo = GroupScoreCard::findOne(['year' => $year, 'team_id' => $teamId, 'quarter' => $quarter]);
                if (!$scoreInfo) {
                    $scoreInfo = new GroupScoreCard();
                    $scoreInfo->year = $year;
                    $scoreInfo->team_id = $teamId;
                    $scoreInfo->quarter = $quarter;
                }

                $scoreInfo->$field = $value;

                if ($scoreInfo->save()) {
                    $db->commit();
                    return ['status' => 1, 'msg' => 'success', 'data' => self::getData($teamId, $year)];
                } else {
                    $db->rollBack();
                    throw new \Exception(Json::encode($scoreInfo->getErrors()));
                }

            } elseif ($saveType == 'team') { //修改团队
                if (!array_key_exists($postData['fieldLabel'], TeamScoreCard::TEAM_FILED_LABEL_MAP)) return ['status' => 0, 'msg' => 'error'];

                $field = TeamScoreCard::TEAM_FILED_LABEL_MAP[$postData['fieldLabel']];

                TeamScoreCard::saveRecord($year, $teamId, $month, $field, $value);

                if ($field == 'team_target') {
                    foreach (GroupScoreCard::QUARTER_RANGE_MONTH as $quarter => $months) {
                        if (in_array($month, $months)) {
                            break;
                        }
                    }

                    $groupTarget = TeamScoreCard::find()
                        ->where(['year' => $year, 'team_id' => $teamId, 'month' => $months])
                        ->sum('team_target');
                    $groupTargetInfo = GroupScoreCard::findOne(['year' => $year, 'team_id' => $teamId, 'quarter' => $quarter]);
                    if (!$groupTargetInfo) {
                        $groupTargetInfo = new GroupScoreCard();
                        $groupTargetInfo->year = $year;
                        $groupTargetInfo->team_id = $teamId;
                        $groupTargetInfo->quarter = $quarter;
                    }

                    $groupTargetInfo->group_target = $groupTarget;
                    if (!$groupTargetInfo->save()) {
                        $db->rollBack();
                        throw new \Exception(Json::encode($groupTargetInfo->getErrors()));
                    }
                }
                $db->commit();
                return ['status' => 1, 'msg' => 'success', 'data' => self::getData($teamId, $year)];
            }
        } catch (\Exception $ex) {
            $db->rollBack();
            return ['status' => 0, 'msg' => 'save error' . $ex->getMessage()];
        }
    }

    /**
     * 编辑模板
     * @param int $rankingId
     * @param string $rankingName
     * @return string
     */
    public function actionTemplateConfig($rankingId = 0, $rankingName = '')
    {
        if ($rankingId == 0) {
            return $this->render('/site/error');
        }
        $scoreCardTemplateDetail = ScoreCardTemplate::getScoreCardTemplate($rankingId);
        return $this->render('template-config', [
            'scoreCardTemplateDetail' => $scoreCardTemplateDetail,
            'rankingId' => $rankingId,
            'rankingName' => $rankingName,
        ]);
    }

    /**
     * 编辑Score
     * @param $employeeId
     * @param $year
     * @param $month
     * @param integer $onlyShow
     * @return string
     */
    public function actionEditScore($employeeId, $year, $month, $onlyShow = 0)
    {

        $scoreDetail = EmployeeScore::find()->select("*,id as employeeScoreId")->where(['employee_id' => $employeeId, 'year' => $year, 'month' => $month])->asArray()->one();

        if (!$scoreDetail) {
            $scoreCardTemplateId = ScoreCardTemplate::find()
                ->alias("sct")
                ->leftJoin(Ranking::tableName() . ' as r', 'r.id = sct.ranking_id')
                ->leftJoin(Employee::tableName() . ' as e', 'e.ranking_id = r.id')
                ->where(['e.id' => $employeeId])
                ->select('sct.id')
                ->one();
            if (empty($scoreCardTemplateId)){
                die("<p style='margin-top: 150px;text-align: center'>" . strtoupper('Please create score card template first!') . "</p>");
            }
            $scoreDetail['employeeScoreId'] = 0;
            $scoreDetail['score_detail'] = ScoreCardTemplate::getScoreCardTemplate($scoreCardTemplateId->id)['score_detail'];
        } else {
            $scoreDetail['score_detail'] = Json::decode($scoreDetail['score_detail'], true);
        }

        $rankingName = Ranking::find()->alias('r')
            ->leftJoin(Employee::tableName() . ' as e', 'e.ranking_id = r.id')
            ->select('r.ranking')
            ->scalar();

        return $this->render('template-config', [
            'scoreCardTemplateDetail' => $scoreDetail,
            'rankingName' => $rankingName,
            'employeeId' => $employeeId,
            'year' => $year,
            'month' => $month,
            'onlyShow' => $onlyShow,
        ]);
    }

    /**
     * 保存Score
     * @return array
     */
    public function actionSaveScore()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $postData = \Yii::$app->request->post();
        $employeeId = ArrayHelper::getValue($postData, 'employeeId', '');
        $employeeScoreId = ArrayHelper::getValue($postData, 'employeeScoreId', '');
        $year = ArrayHelper::getValue($postData, 'year', '');
        $month = ArrayHelper::getValue($postData, 'month', '');
        $data = Json::decode(ArrayHelper::getValue($postData, 'data', '[]'), true);

        $scoreDetailInfo = EmployeeScore::findOne(['employee_id' => $employeeId, 'year' => $year, 'month' => $month]);
        $teamId = Employee::find()->where(['id' => $employeeId])->select('team_id')->scalar();

        if (!$scoreDetailInfo) {
            $scoreDetailInfo = new EmployeeScore();
            $scoreDetailInfo->year = $year;
            $scoreDetailInfo->month = $month;
            $scoreDetailInfo->employee_id = $employeeId;
            $scoreDetailInfo->team_id = $teamId;
        } else {
            if ($scoreDetailInfo->is_edited == 1) {
                return ['status' => 0, 'msg' => 'Only edit once time'];
            }
            $scoreDetailInfo->is_edited = 1;
        }

        $scoreDetailInfo->score_detail = $data;
        $scoreDetailInfo->score = $data[count($data) - 1]['score'];
        if ($scoreDetailInfo->save()) {
            return ['status' => 1, 'msg' => 'save Success'];
        } else {
            return ['status' => 0, 'msg' => 'Save Error'];
        }
    }

    /**
     * 更新模板
     * @return array
     */
    public function actionUpdate()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $postData = Json::decode(\Yii::$app->request->post('data', '[]'), true);
        $rankingId = \Yii::$app->request->post('rankingId', 0);

        $scoreCardTemplate = ScoreCardTemplate::findOne(['ranking_id' => $rankingId]);

        if (!$scoreCardTemplate) {
            $scoreCardTemplate = new ScoreCardTemplate();
            $scoreCardTemplate->created_at = date("Y-m-d H:i:s");
            $scoreCardTemplate->ranking_id = $rankingId;
        }

        $scoreCardTemplate->detail = $postData;
        if ($scoreCardTemplate->save()) {
            return ['status' => 1, 'msg' => 'Success'];
        } else {
            return ['status' => 0, 'msg' => 'Error'];
        }
    }

    /**
     * 打分  通过批准
     * @param int $id
     * @return array
     */
    public function actionApproved($id = 0)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $scoreDetail = EmployeeScore::findOne($id);
            if ($scoreDetail) {
                $scoreDetail->approved_at = date("Y-m-d H:i:s");
                $scoreDetail->is_approved = 1;
                if (!$scoreDetail->save()) {
                    throw new \Exception(Json::encode($scoreDetail->getErrors()));
                }

                return ['status' => 1, 'msg' => 'Success'];
            }
            throw new \Exception('no this records');
        } catch (\Exception $exception) {
            return ['status' => 0, 'msg' => $exception->getMessage()];
        }
    }
}