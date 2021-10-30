<?php


namespace backend\controllers;


use backend\models\Billing;
use backend\models\Employee;
use backend\models\EmployeeScore;
use backend\models\GroupIncentive;
use backend\models\GroupScoreCard;
use backend\models\Ranking;
use backend\models\Reward;
use backend\models\ServiceCommission;
use backend\models\Team;
use backend\models\TeamIncentive;
use backend\models\TeamScoreCard;
use backend\models\TimeSheetApproved;
use backend\models\TimeSheetDate;
use backend\models\Company;
use backend\models\TimeSheet;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

class EmployeeHomeController extends BaseController
{

    public function actionIndex()
    {
        $uid = \Yii::$app->user->getId();

        $date = \Yii::$app->request->get("date", '');

        $where = [];
        if ($date) {
            $realDate = strtotime($date);
            $where = ['sc.month' => date("n", $realDate), 'sc.year' => date("Y", $realDate),'sc.employee_id'=>$uid];
        }

        $nowYear = date("Y");
        $nowMonth = date("n");

        $data = ServiceCommission::find()
            ->alias('sc')
            ->select(new Expression('sc.*,c.company_name,e.employee,t.name team_name,r.ranking,IFNULL(gi.amount,0) gi_amount,IFNULL(ti.amount,0) ti_amount,IFNULL(gi.amount,0) + IFNULL(ti.amount,0) as all_incentive,
            sc.time_sheet_approved_allwance+e.allowance as all_allowance,e.basic_salary,sc.amount+sc.time_sheet_approved_allwance+
            e.allowance+sc.commission+IFNULL(gi.amount,0)+IFNULL(ti.amount,0)+e.basic_salary total'))
            ->leftJoin(Company::tableName() . ' as c','c.id = sc.company_id')
            ->leftJoin(Employee::tableName() . ' as e','e.id = sc.employee_id')
            ->leftJoin(Team::tableName() . ' as t','t.id = sc.team_id')
            ->leftJoin(Ranking::tableName() . ' as r','r.id = sc.ranking_id')
            ->leftJoin(GroupIncentive::tableName().' as gi','gi.is_approved = 1 and sc.employee_id = gi.employee_id and gi.team_id = sc.team_id and gi.year = '.$nowYear.' and gi.target_month = '.$nowMonth)
            ->leftJoin(TeamIncentive::tableName().' as ti','ti.is_approved = 1 and sc.employee_id = ti.employee_id and ti.team_id = sc.team_id and ti.year = '.$nowYear.' and ti.target_month = '.$nowMonth)
            ->where($where)
            ->asArray()
            ->all();
        return $this->render('index', [
            'date' => $date,
            'data' => $data
        ]);
    }

    public function actionHome()
    {

        $uid = \Yii::$app->user->getId();
        $year = date("Y");
        $month = date("n");

        foreach (GroupScoreCard::QUARTER_RANGE_MONTH as $quarter => $months) {
            if (in_array($month, $months)) {
                $quarterMonth = MONTH_MAP[$months[0]] . '-' . MONTH_MAP[$months[2]];
                break;
            }
        }

        //team业绩
        $teamId = Employee::find()->where(['id' => 1])->select('team_id')->scalar();
        $teamScore = TeamScoreCard::findOne(['year' => $year, 'month' => $month, 'team_id' => $teamId]);
        if ($teamScore && $teamScore->reached && $teamScore->team_target>0) {
            $teamScorePercent = intval($teamScore->reached / $teamScore->team_target * 100);
        } else {
            $teamScorePercent = 0;
        }

        //group业绩
        $groupScore = GroupScoreCard::findOne(['year' => $year, 'quarter' => $quarter, 'team_id' => $teamId]);
        if ($groupScore && $groupScore->reached && $groupScore->group_target>0) {
            $groupScorePercent = intval($groupScore->reached / $groupScore->group_target * 100);
        } else {
            $groupScorePercent = 0;
        }

        //打卡信息
        $scoreInfo = EmployeeScore::find()
            ->where(['employee_id' => $uid, 'year' => $year])
            ->asArray()
            ->select('id,team_id,employee_id,score,year,month')
            ->all();

        $scoreInfo = ArrayHelper::index($scoreInfo, 'month');

        $scoreCardList = [];
        foreach (MONTH_MAP as $monthKey => $montVal) {
            $scoreCardList[$monthKey] = $scoreInfo[$monthKey] ?? null;
        }

        $rewardInfo = Reward::find()
            ->alias('r')
            ->leftJoin(Team::tableName().' as t','t.id = r.team_id')
            ->leftJoin(Employee::tableName().' as e','e.team_id = t.id')
            ->asArray()
            ->select(['member','photo'])
            ->orderBy('r.id desc')
            ->one();

        $rewardMembers = Json::decode($rewardInfo['member'],true);

        if (in_array($uid,$rewardMembers??[])){
            $rewardInfo['is_rewarded'] = true;
        }

        //commission信息
        $currentMonthCommissionInfo = ServiceCommission::find()
            ->select(new Expression('sum(commission) current_month_commission'))
            ->where(['employee_id'=>$uid,'year'=>$year,'month'=>$month])
            ->scalar();
        $TotalCommissionInfo = ServiceCommission::find()
            ->select(new Expression('sum(commission) total_commission'))
            ->where(['employee_id'=>$uid])
            ->scalar();

        return $this->render('home', compact([
            'scoreCardList',
            'quarterMonth',
            'month',
            'groupScorePercent',
            'teamScorePercent',
            'rewardInfo',
            'currentMonthCommissionInfo',
            'TotalCommissionInfo',
        ]));
    }

    /**
     * @param string $date
     * @return string
     */
    public function actionTimeSheet($date = '')
    {
        $date = self::getTargetDate($date);

        return $this->render('time-sheet', ['date' => $date]);
    }

    /**
     * @param string $date
     * @return array
     */
    public function actionTimeSheetData($date = '')
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $user = \Yii::$app->user->identity;
        $date = self::getTargetDate($date);
        $uid = $user->getId();
        $data = [
            'defaultData' => self::getDefaultData(),
            'ths' => self::getThs($date),
            'date' => $date,
            'companyList' => Company::getCompanyListByEmployee($uid)
        ];

        $timeSheets = TimeSheet::find()
            ->where(['week_end_date' => $date, 'employee_id' => $uid])
            ->asArray()
            ->all();

        foreach ($timeSheets as &$item) {
            $item['edit'] = false;
            for ($i = 1; $i <= 7; $i++) {
                $item['day_' . $i] = Json::decode($item['day_' . $i], true);
            }
        }

        $data['timeSheets'] = $timeSheets;

        return $data;
    }

    public function actionSave()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if ($user = \Yii::$app->user->isGuest) {
            return ['status' => 0, 'msg' => 'Please Login'];
        }

        $db = \Yii::$app->db->beginTransaction();
        try {
            $postData = Json::decode(\Yii::$app->request->post('data', '[]'), true);
            $weekEndDate = ArrayHelper::getValue($postData, 'weekEndDate', '');
            $status = ArrayHelper::getValue($postData, 'status', '');
            $employeeId = \Yii::$app->user->identity->getId();

            foreach ($postData['timeSheets'] as $data) {
                $timeSheetInfo = TimeSheet::findOne(['id' => $data['id'] ?? 0, 'employee_id' => $employeeId]);
                if (!$timeSheetInfo) {
                    $timeSheetInfo = new TimeSheet();
                    $timeSheetInfo->company_id = $data['company_id'] ?? '';
                    $timeSheetInfo->employee_id = $employeeId;
                }
                if ($timeSheetInfo->status == 1) {
                    continue;
                }
                $timeSheetInfo->status = $status;
                $totalHours = 0;
                $totalOnSite = 0;
                for ($i = 1; $i <= 7; $i++) {
                    $day = 'day_' . $i;
                    $timeSheetInfo->$day = $data[$day] ?? [];
                    $totalHours += $timeSheetInfo->$day['score'];
                    $totalOnSite += $timeSheetInfo->$day['on_site'];
                }
                $timeSheetInfo->task_description = $data['task_description'] ?? '';
                $timeSheetInfo->total_hours = $totalHours;
                $timeSheetInfo->total_on_site = $totalOnSite;
                $timeSheetInfo->week_end_date = $weekEndDate;
                if (!$timeSheetInfo->validate() || !$timeSheetInfo->save()) {
                    throw new \Exception(Json::encode($timeSheetInfo->getErrors()));
                } else {
                    $lastDayTimestamp = strtotime($timeSheetInfo->week_end_date);
                    $timeSheetApprovedInfo = self::dealTimeSheetApproved(date("Y",$lastDayTimestamp), date("n",$lastDayTimestamp), $timeSheetInfo->company_id, $timeSheetInfo->employee_id,$timeSheetInfo->id);
                    for ($i = 6; $i >= 0; $i--) {
                        $date = date("Y-m-d", strtotime("-{$i} day", strtotime($timeSheetInfo->week_end_date)));
                        $timeSheetDate = TimeSheetDate::findOne(['date' => $date, 'time_sheet_id' => $timeSheetInfo->id]);
                        if (!$timeSheetDate) {

                            $teamId = Employee::find()->select('team_id')->where(['id'=>$employeeId])->scalar();

                            $timeSheetDate = new TimeSheetDate();
                            $timeSheetDate->date = $date;
                            $timeSheetDate->year = date("Y",strtotime($date));
                            $timeSheetDate->month = date("n",strtotime($date));
                            $timeSheetDate->time_sheet_id = $timeSheetInfo->id;
                            $timeSheetDate->employee_id = $timeSheetInfo->employee_id;
                            $timeSheetDate->company_id = $timeSheetInfo->company_id;
                            $timeSheetDate->team_id = $teamId;
                            $timeSheetDate->approved_id = $timeSheetApprovedInfo->id ?? 0;
                        }
                        $tmpDay = 'day_' . (7 - $i);
                        $timeSheetDate->hour = $timeSheetInfo->$tmpDay['score'];
                        $timeSheetDate->on_site = $timeSheetInfo->$tmpDay['on_site'];
                        if (!$timeSheetDate->save()) {
                            throw new \Exception(Json::encode($timeSheetInfo->getErrors()));
                        }
                    }
                }
            }
            $db->commit();
            return ['status' => 1, 'msg' => 'Save Success'];
        } catch (\Exception $exception) {
            $db->rollBack();
            return ['status' => 0, 'msg' => $exception->getMessage()];
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id = 0)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $timeSheetInfo = TimeSheet::findOne($id);
        if ($timeSheetInfo) {
            if ($timeSheetInfo->delete()) {
                return ['status' => 1, 'msg' => 'Delete Success'];
            }
        }

        return ['status' => 0, 'msg' => 'Delete Error'];
    }

    /**
     * @param string $date
     * @return false|string
     */
    public static function getTargetDate($date = '')
    {
        if (empty($date)) {
            return date("Y-m-d", strtotime('Friday'));
        } else {
            return date("Y-m-d", strtotime('Friday', strtotime($date)));
        }
    }

    /**
     * @return array[]
     */
    public static function getDefaultData()
    {
        return [
            'company_id' => "",
            'id' => 0,
            'edit' => true,
            'on_site_edit' => false,
            'day_1' => ['score' => 0, 'on_site' => 0],
            'day_2' => ['score' => 0, 'on_site' => 0],
            'day_3' => ['score' => 0, 'on_site' => 0],
            'day_4' => ['score' => 0, 'on_site' => 0],
            'day_5' => ['score' => 0, 'on_site' => 0],
            'day_6' => ['score' => 0, 'on_site' => 0],
            'day_7' => ['score' => 0, 'on_site' => 0],
            'task_description' => '',
            'total_hours' => 0,
        ];
    }

    /**
     * @param $date
     * @return string[]
     */
    public static function getThs($date)
    {
        $ths = [
            "Company Name",
        ];
        for ($i = 6; $i >= 0; $i--) {
            $ths[] = date("Y-m-d", strtotime("-{$i} day", strtotime($date))) . ' on_site';
        }
        $ths[] = "Task Description";
        $ths[] = "Total hours";
        $ths[] = "Option";

        return $ths;
    }

    /**
     * 时间表批准信息
     * @param $year
     * @param $month
     * @param $companyId
     * @param $employeeId
     * @param $timeSheetId
     * @return TimeSheetApproved
     * @throws \Exception
     */
    public static function dealTimeSheetApproved($year, $month, $companyId, $employeeId,$timeSheetId)
    {
        $approvedInfo = TimeSheetApproved::findOne(['year' => $year, 'month' => $month, 'company_id' => $companyId, 'employee_id' => $employeeId,'time_sheet_id'=>$timeSheetId]);

        $teamId = Employee::find()->select("team_id")->where(['id'=>$employeeId])->scalar();
        if (empty($teamId)){
            throw new \Exception("Error");
        }
        if (!$approvedInfo) {
            $approvedInfo = new TimeSheetApproved();
            $approvedInfo->year = $year;
            $approvedInfo->month = $month;
            $approvedInfo->company_id = $companyId;
            $approvedInfo->team_id = $teamId;
            $approvedInfo->employee_id = $employeeId;
            $approvedInfo->time_sheet_id = $timeSheetId;
            if (!$approvedInfo->save()) {
                throw new \Exception(Json::encode($approvedInfo->getErrors()));
            }
        }
        return $approvedInfo;
    }
}