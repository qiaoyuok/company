<?php


namespace backend\controllers;


use backend\models\Billing;
use backend\models\Company;
use backend\models\Employee;
use backend\models\EmployeeScore;
use backend\models\GroupIncentive;
use backend\models\GroupScoreCard;
use backend\models\Ranking;
use backend\models\ServiceCommission;
use backend\models\Team;
use backend\models\TeamIncentive;
use backend\models\TeamScoreCard;
use backend\models\TimeSheet;
use backend\models\TimeSheetApproved;
use backend\models\TimeSheetDate;
use Codeception\Platform\Group;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

class CommissionListController extends BaseController
{
    public function actionIndex()
    {
        $date = \Yii::$app->request->get("date", '');

        $where = [];
        if ($date) {
            $realDate = strtotime($date);
            $where = ['sc.month' => date("n", $realDate), 'sc.year' => date("Y", $realDate)];
        }

        $nowYear = date("Y");
        $nowMonth = date("n");

        $data = ServiceCommission::find()
            ->alias('sc')
            ->select(new Expression('sc.*,c.company_name,e.employee,t.name team_name,r.ranking,IFNULL(gi.amount,0) gi_amount,IFNULL(ti.amount,0) ti_amount,IFNULL(gi.amount,0) + IFNULL(ti.amount,0) as all_incentive,
            sc.time_sheet_approved_allwance+e.allowance as all_allowance,e.basic_salary,sc.commission+IFNULL(gi.amount,0) + IFNULL(ti.amount,0)+sc.time_sheet_approved_allwance+e.allowance total'))
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

    public function actionIndexOld()
    {
        $companyName = \Yii::$app->request->get("company_name", '');
        $date = \Yii::$app->request->get("date", '');
        $teamId = \Yii::$app->request->get("team_id", 0);

        $where = [];
        if ($date) {
            $where = ['tsa.month' => date("n", strtotime($date)), 'tsa.year' => date("Y", strtotime($date))];
        }

        $companyNameWhere = [];
        if ($companyName) {
            $companyNameWhere = ['like', 'c.company_name', $companyName];
        }

        $teamWhere = [];
        if ($teamId) {
            $teamWhere = ['e.team_id' => $teamId];
        }

        $timeSheets = TimeSheetDate::find()
            ->alias('tsd')
            ->leftJoin(TimeSheet::tableName() . ' as ts', 'ts.id = tsd.time_sheet_id')
            ->leftJoin(Company::tableName() . ' as c', 'c.id = ts.company_id')
            ->leftJoin(Employee::tableName() . ' as e', 'e.id = ts.employee_id')
            ->leftJoin(Team::tableName() . ' as t', 't.id = e.team_id')
            ->leftJoin(TimeSheetApproved::tableName() . ' as tsa', 'tsa.id = tsd.approved_id')
            ->leftJoin(Billing::tableName() . ' as b', 'b.company_id = tsa.company_id and b.year = tsa.year and b.month = tsa.month')
            ->leftJoin(EmployeeScore::tableName() . ' as es', 'es.year = tsa.year and es.month = tsa.month and es.employee_id = tsa.employee_id')
            ->leftJoin(Ranking::tableName() . ' as r', 'r.id = e.ranking_id')
            ->leftJoin(TeamIncentive::tableName(). ' as ti','ti.target_year = tsa.year and ti.target_month = tsa.month and ti.employee_id = tsa.employee_id and ti.is_approved=1')
            ->leftJoin(GroupIncentive::tableName(). ' as gi','gi.target_year = tsa.year and gi.target_month = tsa.month and gi.employee_id = tsa.employee_id and ti.is_approved=1')
            ->select('e.basic_salary,e.allowance eAllowance,r.ranking,t.name team_name,c.company_name,e.employee,
            tsd.*,tsa.is_approved,tsa.allowance,ts.company_id,tsa.month,tsa.year,b.billed_amount,b.billed_amount_detail,
            b.approved_at,tsa.is_approved,es.score,c.id company_id,e.id employee_id,gi.amount groupIncentive,
            ti.amount teamIncentive,tsa.commission')
            ->where($where)
            ->andWhere($companyNameWhere)
            ->andWhere($teamWhere)
            ->andWhere(['ts.status' => 1, 'b.is_approved' => 1, 'es.is_approved' => 1])
            ->orderBy('tsd.approved_id desc')
            ->asArray()
            ->all();
        $timeSheets = ArrayHelper::index($timeSheets, null, ['year', 'month', 'employee_id', 'company_id']);
        $data = [];
        foreach ($timeSheets as $item) {
            foreach ($item as $citem) {
                foreach ($citem as $ccitem) {
                    foreach ($ccitem as $cccitem) {
                        $tmpData = [];
                        $tmpData['edit'] = false;
                        $tmpData['allowanceOrigin'] = 0;
                        $tmpData['company_name'] = $cccitem[0]['company_name'] ?? '';
                        $tmpData['employee'] = $cccitem[0]['employee'] ?? '';
                        $tmpData['basic_salary'] = $cccitem[0]['basic_salary'] ?? '';
                        $tmpData['eAllowance'] = $cccitem[0]['eAllowance'] ?? '';
                        $tmpData['team_name'] = $cccitem[0]['team_name'] ?? '';
                        $tmpData['allowance'] = $cccitem[0]['allowance'] ?? 0;
                        $tmpData['ranking'] = $cccitem[0]['ranking'] ?? 0;
                        $tmpData['TotalAllowance'] = $tmpData['allowance'] + $tmpData['eAllowance'];  //总的津贴
                        $tmpData['approved_id'] = $cccitem[0]['approved_id'] ?? 0;
                        $tmpData['score'] = $cccitem[0]['score'] ?? 0;
                        $tmpData['is_approved'] = $cccitem[0]['is_approved'] ?? 0;
                        $tmpData['is_approved'] = $cccitem[0]['is_approved'] ?? 0;
                        $tmpData['approved_at'] = $cccitem[0]['approved_at'] ? $cccitem[0]['approved_at'] : '/';
                        $tmpData['billed_amount_detail'] = $cccitem[0]['billed_amount_detail'] ? Json::decode($cccitem[0]['billed_amount_detail'], true) : [];
                        $tmpData['billed_amount'] = $tmpData['billed_amount_detail'][1]['price'] ?? 0;
                        $hoursArr = ArrayHelper::getColumn($cccitem, ['hour']);
                        $onSiteArr = ArrayHelper::getColumn($cccitem, ['on_site']);
                        $tmpData['totalHours'] = array_sum($hoursArr);
                        $tmpData['onSite'] = array_sum($onSiteArr);
                        $tmpData['Incentive'] = ($cccitem[0]['groupIncentive'] ?? 0)+($cccitem[0]['teamIncentive'] ?? 0);
                        $tmpData['commission'] = $cccitem[0]['commission']??0;
                        $tmpData['total'] = round(array_sum([$tmpData['billed_amount'], $tmpData['commission'], $tmpData['basic_salary'], $tmpData['TotalAllowance']]), 2);
                        $data[] = $tmpData;
                    }
                }
            }
        }
        return $this->render('index', [
            'companyName' => $companyName,
            'date' => $date,
            'teamId' => $teamId,
            'data' => $data
        ]);
    }

    public function actionTeam()
    {
        $teamId = \Yii::$app->request->get('team_id', '');
        $date = \Yii::$app->request->get('date', '');

        $data = [
            'date' => $date,
            'teamId' => $teamId,
            'employees' => [],
            'Incentive' => 0,
            'year' => 0,
            'month' => 0,
        ];
        try {
            if ($teamId && $date) {

                $month = date("m", strtotime($date));
                $year = date("Y", strtotime($date));

                //1、先获取该月份的既定  激励金
                $teamScoreInfo = TeamScoreCard::find()->where(['team_id' => $teamId, 'year' => $year, 'month' => $month])->one();

                if (!$teamScoreInfo) {
                    throw new \Exception('no team score');
                }
                $data['Incentive'] = $teamScoreInfo->Incentive;
                $data['year'] = $year;
                $data['month'] = $month;

                //2、获取团队成员
                $employees = Employee::find()
                    ->alias('e')
                    ->leftJoin(Team::tableName() . ' as t', 't.id = e.team_id')
                    ->leftJoin(Ranking::tableName() . ' as r', 'r.id = e.ranking_id')
                    ->leftJoin(TeamIncentive::tableName() . ' as ti', 'ti.employee_id = e.id and ti.year =' . $year . ' and ti.month = ' . $month)
                    ->where(['t.id' => $teamId])
                    ->select('t.name,e.employee,r.ranking,ti.amount,ti.is_approved,e.id employee_id,ti.id')
                    ->asArray()
                    ->all();
                foreach ($employees as &$employee) {
                    $employee['editStatus'] = false;
                    $employee['amount'] = $employee['amount'] ?? 0;
                    $employee['tmpAmount'] = $employee['amount'];
                }

                $data['employees'] = $employees;

            }
        } catch (\Exception $exception) {

        }

        return $this->render('team', $data);
    }

    /**
     * 团队激励金通过
     * @param $id
     * @return array
     */
    public function actionTeamApproved($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $info = TeamIncentive::findOne($id);

        if ($info) {
            $info->approved_at = date("Y-m-d H:i:s");
            $info->is_approved = 1;
            if ($info->save()) {
                return ['status' => 1, 'msg' => 'save success'];
            }
        }

        return ['status' => 0, 'msg' => 'save error'];
    }

    /**
     * @param $employeeId
     * @param $amount
     * @param $year
     * @param $month
     * @return array
     */
    public function actionSaveTeamAmount($employeeId, $amount, $year, $month)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $teamId = Employee::find()->where(['id' => $employeeId])->select('team_id')->scalar();
        $info = TeamIncentive::find()
            ->where(['team_id' => $teamId, 'employee_id' => $employeeId, 'year' => $year, 'month' => $month])
            ->one();
        if (!$info) {
            $info = new TeamIncentive();
            $info->team_id = $teamId;
            $info->employee_id = $employeeId;
            $info->year = $year;
            $info->month = $month;
            $targetDate = strtotime("+2 month", strtotime($year . '-' . $month));
            $info->target_month = date("m", $targetDate);
            $info->target_year = date("Y", $targetDate);
            $info->is_approved = 0;
        }
        $info->amount = $amount;

        if (!$info->save()) {
            return ['status' => 0, 'msg' => 'save error'];
        } else {
            return ['status' => 1, 'msg' => 'save success'];
        }
    }

    public function actionGroup()
    {
        $teamId = \Yii::$app->request->get('team_id', '');
        $date = \Yii::$app->request->get('date', '');

        $data = [
            'date' => $date,
            'teamId' => $teamId,
            'employees' => [],
            'Incentive' => 0,
            'year' => 0,
            'quarter' => 0,
        ];

        try {
            if ($teamId && $date) {

                $month = date("n", strtotime($date));

                foreach (GroupScoreCard::QUARTER_RANGE_MONTH as $quarter => $months) {
                    if (in_array($month, $months)) {
                        $data['quarter'] = $quarter;
                        break;
                    }
                }

                $year = date("Y", strtotime($date));

                //1、先获取该月份的既定  激励金
                $teamScoreInfo = GroupScoreCard::find()->where(['team_id' => $teamId, 'year' => $year, 'quarter' => $quarter])->one();

                if (!$teamScoreInfo) {
                    throw new \Exception('no team score');
                }
                $data['Incentive'] = $teamScoreInfo->Incentive;
                $data['year'] = $year;

                //2、获取团队成员
                $employees = Employee::find()
                    ->alias('e')
                    ->leftJoin(Team::tableName() . ' as t', 't.id = e.team_id')
                    ->leftJoin(Ranking::tableName() . ' as r', 'r.id = e.ranking_id')
                    ->leftJoin(GroupIncentive::tableName() . ' as gi', 'gi.employee_id = e.id and gi.year =' . $year . ' and gi.quarter = ' . $quarter)
                    ->where(['t.id' => $teamId])
                    ->select('t.name,e.employee,r.ranking,gi.amount,gi.is_approved,e.id employee_id,gi.id')
                    ->asArray()
                    ->all();
                foreach ($employees as &$employee) {
                    $employee['editStatus'] = false;
                    $employee['amount'] = $employee['amount'] ?? 0;
                    $employee['tmpAmount'] = $employee['amount'];
                }

                $data['employees'] = $employees;

            }
        } catch (\Exception $exception) {

        }

        return $this->render('group', $data);
    }

    /**
     * 团队激励金通过
     * @param $id
     * @return array
     */
    public function actionGroupApproved($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $info = GroupIncentive::findOne($id);

        if ($info) {
            $info->approved_at = date("Y-m-d H:i:s");
            $info->is_approved = 1;
            if ($info->save()) {
                return ['status' => 1, 'msg' => 'save success'];
            }
        }

        return ['status' => 0, 'msg' => 'save error'];
    }

    /**
     * @param $employeeId
     * @param $amount
     * @param $year
     * @param $quarter
     * @return array
     */
    public function actionSaveGroupAmount($employeeId, $amount, $year, $quarter)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $teamId = Employee::find()->where(['id' => $employeeId])->select('team_id')->scalar();
        $info = GroupIncentive::find()
            ->where(['team_id' => $teamId, 'employee_id' => $employeeId, 'year' => $year, 'quarter' => $quarter])
            ->one();
        if (!$info) {
            $info = new GroupIncentive();
            $info->team_id = $teamId;
            $info->employee_id = $employeeId;
            $info->year = $year;
            $info->quarter = $quarter;
            $targetDate = strtotime("+4 month", strtotime($year . '-' . $quarter * 3));
            $info->target_month = date("m", $targetDate);
            $info->target_year = date("Y", $targetDate);
            $info->is_approved = 0;
        }
        $info->amount = $amount;

        if (!$info->save()) {
            return ['status' => 0, 'msg' => 'save error'];
        } else {
            return ['status' => 1, 'msg' => 'save success'];
        }
    }
}