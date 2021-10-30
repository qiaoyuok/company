<?php

namespace backend\controllers;

use backend\models\Company;
use backend\models\Employee;
use backend\models\EmployeeScore;
use backend\models\GroupScoreCard;
use backend\models\Ranking;
use backend\models\ServiceCommission;
use backend\models\Team;
use backend\models\TimeSheet;
use backend\models\TimeSheetApproved;
use backend\models\TimeSheetDate;
use Yii;
use backend\models\Billing;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * BillingController implements the CRUD actions for Billing model.
 */
class BillingController extends BaseController
{
    public function actionIndex()
    {
        $companyName = \Yii::$app->request->get("companyName", '');
        $date = \Yii::$app->request->get("date", '');

        $where = [];
        if ($date) {
            $where = ['b.month' => date("n", strtotime($date)), 'b.year' => date("Y", strtotime($date))];
        }

        $companyNameWhere = [];
        if ($companyName) {
            $companyNameWhere = ['like', 'c.company_name', $companyName];
        }

        $data = Billing::find()
            ->alias('b')
            ->leftJoin(Company::tableName() . ' as c', 'c.id = b.company_id')
            ->where($where)
            ->andWhere($companyNameWhere)
            ->select('b.*,c.company_name')
            ->orderBy("id desc")
            ->asArray()
            ->all();

        foreach ($data as &$item) {
            $item['billed_amount_detail'] = Json::decode($item['billed_amount_detail'], true);
        }
        return $this->render('index', [
            'companyName' => $companyName,
            'date' => $date,
            'data' => $data
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $billingInfo = new Billing();

        return $this->render('create', [
            'billingInfo' => $billingInfo,
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $billingInfo = Billing::find()->where(['id' => $id])->asArray()->one();

        return $this->render('update', [
            'billingInfo' => $billingInfo
        ]);
    }

    /**
     * 更新Amount信息
     * @return array
     */
    public function actionSaveAmount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Json::decode(Yii::$app->request->post('data', '[]'), true);
        $id = Yii::$app->request->post('id', 0);
        $teamId = Yii::$app->request->post('teamId', 0);
        $companyId = Yii::$app->request->post('companyId', 0);
        $invoiceNumber = Yii::$app->request->post('invoice_number', '');
        $invoiceDate = Yii::$app->request->post('invoice_date', '');

        $billingInfo = Billing::findOne($id);

        if (!$billingInfo) {

            //为了获取上个月的Score
            $lastMonthDate = strtotime("-1 month");
            $billingInfo = new Billing();
            $billingInfo->team_id = $teamId;
            $billingInfo->date = date("Y-m-d");
            $billingInfo->year = date("Y", $lastMonthDate);
            $billingInfo->month = date("n", $lastMonthDate);
            $billingInfo->real_month = date("n");
            $billingInfo->company_id = $companyId;


            $billingInfo->time_sheet = intval(TimeSheetDate::find()
                ->alias('tsd')
                ->innerJoin(TimeSheetApproved::tableName() . ' as tad', 'tad.time_sheet_id = tsd.time_sheet_id')
                ->where(['tsd.team_id' => $teamId, 'tsd.company_id' => $companyId, 'tsd.is_calc' => 2, 'tad.is_approved' => 1])
                ->sum('tsd.hour'));


            $timeSheetDatas = TimeSheetDate::find()
                ->alias('tsd')
                ->innerJoin(TimeSheetApproved::tableName() . ' as tad', 'tad.time_sheet_id = tsd.time_sheet_id')
                ->where(['tsd.team_id' => $teamId, 'tsd.company_id' => $companyId, 'tsd.is_calc' => 2, 'tad.is_approved' => 1])
                ->asArray()
                ->all();

            $timeSheetDataIds = ArrayHelper::getColumn($timeSheetDatas, 'time_sheet_id');
            TimeSheetDate::updateAll(['is_calc' => 1], ['time_sheet_id' => $timeSheetDataIds]);
            $billingInfo->on_site_allowance = TimeSheetApproved::find()
                    ->where(['team_id' => $teamId, 'company_id' => $companyId, 'to_billing' => 2, 'is_approved' => 1])
                    ->sum('allowance') ?? 0;

        }

        $billedAmount = 0;
        foreach ($data as &$item) {
            $billedAmount += $item['price'];
        }

        $billingInfo->billed_amount_detail = $data;
        $billingInfo->billed_amount = $billedAmount;
        $billingInfo->invoice_number = $invoiceNumber;
        $billingInfo->invoice_date = $invoiceDate;
        if ($billingInfo->save()) {

            TimeSheetApproved::updateAll(['to_billing' => 1], ['time_sheet_id' => $timeSheetDataIds]);

            return ['status' => 1, 'msg' => 'Success'];
        }
        return ['status' => 1, 'msg' => $billingInfo->getErrors()];
    }

    /**
     * Boss审核通过
     * @param int $id
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionApproved($id = 0)
    {
        $db = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $billingInfo = Billing::findOne(['id' => $id, 'is_approved' => 0]);
            if (!$billingInfo) {
                return ['status' => 0, 'msg' => 'Billing is not exists'];
            }
            $billingInfo->is_approved = 1;
            $billingInfo->approved_at = date("Y-m-d H:i:s");

            $teamId = $billingInfo->team_id;

            $fee = $billingInfo->billed_amount_detail[1]['price'] ?? 0;

            $companyInfo = Company::findOne($billingInfo->company_id);

            if (!$companyInfo) {
                throw new \Exception("Unknow error");
            }

            foreach ($companyInfo->person_assign as $employeeId) {
                $employeeScoreInfo = EmployeeScore::findOne(['team_id' => $teamId, 'employee_id' => $employeeId, 'year' => $billingInfo->year, 'month' => $billingInfo->month, 'is_approved' => 1]);
                if (!$employeeScoreInfo) {
                    throw new \Exception('Employee ID ' . $employeeId . ' has no Last month Score or not approved');
                }

                //把 Commission放到billing里去
                $commission = self::calcCommission($employeeId, $billingInfo->company_id, $billingInfo->year, $billingInfo->month, $billingInfo->billed_amount_detail[1]['price' ?? 0]);
                $billingInfo->commission = $commission;

                //生成Commission
                $commissionInfo = ServiceCommission::findOne(['year' => $billingInfo->year, 'month' => $billingInfo->real_month, 'company_id' => $billingInfo->company_id, 'team_id' => $billingInfo->team_id, 'employee_id' => $employeeId]);

                $employeeInfo = Employee::findOne($employeeId);

                if (empty($commissionInfo)) {
                    $commissionInfo = new ServiceCommission();
                    $commissionInfo->commission = $commission;
                    $commissionInfo->year = $billingInfo->year;
                    $commissionInfo->month = $billingInfo->real_month;
                    $commissionInfo->team_id = $billingInfo->team_id;
                    $commissionInfo->company_id = $billingInfo->company_id;
                    $commissionInfo->employee_id = $employeeId;
                    $commissionInfo->ranking_id = $employeeInfo->ranking_id;
                    $commissionInfo->amount = $billingInfo->billed_amount_detail[1]['price'] ?? 0;
                    $commissionInfo->time_sheet = $billingInfo->time_sheet;
                    $commissionInfo->score = $employeeScoreInfo->score;
                    $commissionInfo->time_sheet_approved_allwance = $billingInfo->on_site_allowance;
                } else {
                    $commissionInfo->commission += $commission;
                    $commissionInfo->amount += $billingInfo->billed_amount_detail[1]['price'] ?? 0;
                    $commissionInfo->time_sheet += $billingInfo->time_sheet;
                    $commissionInfo->time_sheet_approved_allwance += $billingInfo->on_site_allowance;
                }

                if (!$commissionInfo->save()) {
                    throw new \Exception('Save commission error EmployeeId:' . $employeeId);
                }
            }

            if ($billingInfo->save()) {
                GroupScoreCard::saveReached($teamId, $fee, $billingInfo->year, $billingInfo->month);
                $db->commit();
                return ['status' => 1, 'msg' => 'Success'];
            } else {
                throw new \Exception(Json::encode($billingInfo->getErrors()));
            }
        } catch (\Exception $exception) {
            $db->rollBack();
            return ['status' => 0, 'msg' => 'Error - ' . $exception->getMessage()];
        }
    }

    /**
     * @param string $date
     * @return false|string
     */
    public static function getTargetDate($date = '')
    {
        if (empty($date)) {
            $date = date("Y-m-d", strtotime('Saturday'));
        } else {
            $date = date("Y-m-d", strtotime('Saturday', strtotime($date)));
        }
        return $date;
    }

    /**
     * 佣金计算
     * @param int $employeeId
     * @param int $companyId
     * @param int $year
     * @param int $month
     * @param int $amount
     * @return float|int|void
     */
    public static function calcCommission($employeeId = 0, $companyId = 0, $year = 0, $month = 0, $amount = 0)
    {
        //1、获取共事所有员工
        $companyAssignEmployees = Json::decode(Employee::find()
            ->alias("e")
            ->leftJoin(Team::tableName() . ' as t', 't.id = e.team_id')
            ->leftJoin(Company::tableName() . ' as c', 'c.team_id = t.id')
            ->select('c.person_assign')
            ->where(['e.id' => $employeeId, 'c.id' => $companyId])
            ->asArray()
            ->scalar(), true);

        //获取员工打分
        $employeeRankingInfo = Employee::find()
            ->select('e.id employee_id,r.commission,r.ranking,es.score,r.role_id')
            ->alias('e')
            ->leftJoin(Ranking::tableName() . ' as r', 'r.id = e.ranking_id')
            ->leftJoin(EmployeeScore::tableName() . ' es', 'es.employee_id = e.id')
            ->where(['e.id' => $companyAssignEmployees, 'es.year' => $year, 'es.month' => $month])
            ->asArray()
            ->all();

        $noLeader = true;  //默认没领到
        $leaderId = 0;
        foreach ($employeeRankingInfo as $item) {

            if ($item['role_id'] == 3) {
                $noLeader = false;   //有领导
                $leaderId = $item['employee_id'];
                break;
            }
        }

        if ($noLeader) {  //没领到的计算
            foreach ($employeeRankingInfo as $item) {
                if ($item['employee_id'] == $employeeId) {
                    if ($item['score'] >= 80) {
                        return $item['commission'] / 100 * $amount;   //分值超过80分  正常计算
                    } else {
                        return $item['commission'] / 100 * $amount * 0.8; //分值低于80分  原有的基础上减少20%
                    }
                }
            }
        } elseif (!$noLeader && count($employeeRankingInfo) == 1) { //领导自己的单子   不看打分 直接按比例计算
            foreach ($employeeRankingInfo as $item) {
                if ($item['employee_id'] == $employeeId) {
                    return $item['commission'] / 100 * $amount;   //分值超过80分  正常计算
                }
            }
        } else {  //领导+员工的单子
            $maxPercent = 55;
            $subTotal = 0;
            foreach ($employeeRankingInfo as $item) {
                if ($leaderId != $employeeId) {
                    $subTotal += $item['commission'];
                }
                if ($item['employee_id'] == $employeeId && $leaderId != $employeeId) {  //普通员工的
                    if ($item['score'] >= 80) {
                        return $item['commission'] / 100 * $amount;   //分值超过80分  正常计算
                    } else {
                        return $item['commission'] / 100 * $amount * 0.8; //分值低于80分  原有的基础上减少20%
                    }
                }
            }
            return (($maxPercent - $subTotal) > 30 ? 30 : ($maxPercent - $subTotal)) * $amount;
        }
    }
}
