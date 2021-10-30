<?php


namespace backend\models;

use yii\base\Widget;

class ConfigScoreCardModal extends Widget
{
    public $rankingId;

    public function run()
    {

        $scoreCardTemplateDetail = ScoreCardTemplate::find()->where(['ranking_id' => $this->rankingId])->asArray()->all();

//        if ($scoreCardTemplateDetail){
//            var_dump($scoreCardTemplateDetail);exit;
//        }

        return $this->render('/modals/config-score-card', compact('scoreCardTemplateDetail'));
    }
}