<?php

namespace backend\controllers;

use backend\models\Company;
use backend\models\Team;
use Yii;
use backend\forms\ClearCache;
use common\helpers\ResultHelper;
use yii\helpers\Json;

/**
 * 主控制器
 *
 * Class MainController
 * @package backend\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MainController extends BaseController
{
    /**
     * 系统首页
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->renderPartial($this->action->id, []);
    }

    /**
     * 子框架默认主页
     *
     * @return string
     */
    public function actionSystem()
    {
        $companyNameWhere = [];
        $companyNumberWhere = [];

        $companyName = Yii::$app->request->get('company_name','');
        $companyNumber = Yii::$app->request->get('company_number','');

        if ($companyName){
            $companyNameWhere = ['like','company_name',$companyName];
        }

        if ($companyNumber){
            $companyNumberWhere = ['like','company_number',$companyNumber];
        }

        $companyList = Company::find()
            ->alias('c')
            ->select('c.*,t.name team_name')
            ->orderBy('id asc')
            ->where($companyNameWhere)
            ->andWhere($companyNumberWhere)
            ->andWhere(['c.status'=>1])
            ->leftJoin(Team::tableName() . ' as t', 't.id = c.team_id')
            ->asArray()
            ->all();
        foreach ($companyList as &$item) {
            $item['person_assign'] = Json::decode($item['person_assign'],true);
        }
        return $this->render('/company/index', [
            'companyList' => $companyList
        ]);
    }

    /**
     * 用户指定时间内数量
     *
     * @param $type
     * @return array
     */
    public function actionMemberBetweenCount($type)
    {
        $data = Yii::$app->services->member->getBetweenCountStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * 充值统计
     *
     * @param $type
     * @return array
     */
    public function actionMemberRechargeStat($type)
    {
        $data = Yii::$app->services->memberCreditsLog->getRechargeStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * 用户指定时间内消费日志
     *
     * @param $type
     * @return array
     */
    public function actionMemberCreditsLogBetweenCount($type)
    {
        $data = Yii::$app->services->memberCreditsLog->getBetweenCountStat($type);

        return ResultHelper::json(200, '获取成功', $data);
    }

    /**
     * 清理缓存
     *
     * @return string
     */
    public function actionClearCache()
    {
        $model = new ClearCache();
        if ($model->load(Yii::$app->request->post())) {
            return $model->save()
                ? $this->message('清理成功', $this->refresh())
                : $this->message($this->getError($model), $this->refresh(), 'error');
        }

        return $this->render($this->action->id, [
            'model' => $model
        ]);
    }
}