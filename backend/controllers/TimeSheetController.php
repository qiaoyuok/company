<?php


namespace backend\controllers;


use backend\models\Billing;
use backend\models\TimeSheetApproved;
use backend\models\TimeSheetDate;
use backend\models\Company;
use backend\models\Employee;
use backend\models\TimeSheet;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;

class TimeSheetController extends BaseController
{
    public function actionIndex()
    {
        $companyName = \Yii::$app->request->get("company_name", '');
        $date = \Yii::$app->request->get("date", '');
        $teamId = \Yii::$app->request->get("team_id", 0);

        $where = [];
        if ($date) {
            $where = ['ts.month' => date("n", strtotime($date)),'ts.year' => date("Y", strtotime($date))];
        }

        $companyWhere = [];
        if ($companyName) {

           $companies =  Company::find()->select('id')->where(['like', 'company_name', $companyName])->all();
           if ($companies){
               $companyWhere = ['ts.company_id'=>ArrayHelper::getColumn($companies,'id')];
           }
        }

        $teamWhere = [];
        if ($teamId) {
            $employees = Employee::find()->where(['team_id'=>$teamId])->all();
            if ($employees){
                $teamWhere = ['ts.employee_id' => ArrayHelper::getColumn($employees,'id')];
            }
        }

        $data = TimeSheet::find()
            ->select('ts.id,ts.total_hours,ts.total_on_site,c.company_name,e.employee,tsa.allowance,tsa.is_approved,tsa.id approved_id')
            ->alias("ts")
            ->leftJoin(Company::tableName().' as c','c.id = ts.company_id')
            ->leftJoin(Employee::tableName().' as e','e.id = ts.employee_id')
            ->leftJoin(TimeSheetApproved::tableName().' as tsa','tsa.time_sheet_id = ts.id')
            ->where($where)
            ->andWhere($companyWhere)
            ->andWhere($teamWhere)
            ->orderBy('ts.id desc')
            ->asArray()
            ->all();

        foreach ($data as &$dataItem){
            $dataItem['edit'] = false;
        }

        return $this->render('index', [
            'companyName' => $companyName,
            'date' => $date,
            'teamId' => $teamId,
            'data' => $data
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionSave()
    {
        $db = \Yii::$app->db->beginTransaction();
        try {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = \Yii::$app->request->post('id', 0);
            $field = \Yii::$app->request->post('field', '');
            $value = \Yii::$app->request->post('value', 0);

            if (!in_array($field, ['allowance', 'is_approved']) || ($field === 'is_approved' && $value != 1)) {
                throw new \Exception('Params error');
            }
            $timeSheetApprovedInfo = TimeSheetApproved::findOne($id);
            if ($timeSheetApprovedInfo) {
                $timeSheetApprovedInfo->$field = $value;
                if ($field == 'is_approved') {

                    //把Time sheet 和 Allowance 加到billing里去
                    $billingInfo = Billing::findOne(['team_id'=>$timeSheetApprovedInfo->team_id,'company_id'=>$timeSheetApprovedInfo->company_id,'is_approved'=>0]);

                    //Billing如果没有新的  就先标记下这个timesheet
                    if (!$billingInfo){
                        $timeSheetApprovedInfo->to_billing = 2;
                    }else{
                        //计算工时
                        $billingInfo->time_sheet += TimeSheetDate::find()
                            ->where(['approved_id'=>$timeSheetApprovedInfo->id])
                            ->sum('hour');

                        //计算津贴
                        $billingInfo->on_site_allowance += $timeSheetApprovedInfo->allowance;

                        if (!$billingInfo->save()){
                            throw new \Exception(Json::encode($billingInfo->getErrors()));
                        }
                        $timeSheetApprovedInfo->to_billing = 1;
                        $timeSheetApprovedInfo->billing_id = $billingInfo->id;

                        TimeSheetDate::updateAll(['is_calc'=>1],['time_sheet_id'=>$timeSheetApprovedInfo->time_sheet_id]);
                    }

                    $timeSheetApprovedInfo->approved_at = date("Y-m-d H:i:s");
                }
                if (!$timeSheetApprovedInfo->save()) {
                    throw new \Exception(Json::encode($timeSheetApprovedInfo->getErrors()));
                }
                return ['status' => 1, 'msg' => 'Save Success'];
            }
        } catch (\Exception $exception) {
            $db->rollBack();
            return ['status' => 0, 'msg' => $exception->getMessage()];
        }
    }
}