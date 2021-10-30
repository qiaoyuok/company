<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%service_score_card_template_detail}}".
 *
 * @property int $id
 * @property string $description 描述内容
 * @property int $sort 排序；
 * @property int $parent_id 父级ID
 * @property int $is_part_title 是part标题；1：是；2：不是
 * @property int $total_type 汇总类型；0：不是；1：A汇总；2：B汇总；3：总汇
 */
class ScoreCardTemplateDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_score_card_template_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'parent_id', 'is_part_title', 'total_type', 'template_score_card_id'], 'integer'],
            [['description'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'sort' => 'Sort',
            'parent_id' => 'Parent ID',
            'is_part_title' => 'Is Part Title',
            'total_type' => 'Total Type',
            'template_score_card_id' => 'template_score_card_id',
        ];
    }

    /**
     * @return array
     */
    public static function getTopDetail()
    {
        $detailList = self::find()
            ->where(['parent_id' => 0])
            ->all();

        $detailListArr = [];
        $detailListArr[0] = 'Select Parent';
        foreach ($detailList as $detail) {
            $detailListArr[$detail->id] = $detail->description;
        }

        return $detailListArr;
    }

    /**
     * 更新
     * @param $item
     * @throws \Exception
     */
    public static function updateItem($item)
    {
        $model = self::findOne($item['id'] ?? 0);
        if (!$model) {
            $model = new self();
        }

        $model->description = $item['description'] ?? '';
        $model->parent_id = $item['parent_id'] ?? 0;
        $model->sort = $item['sort'] ?? 0;
        if (!$model->save()) {
            throw new \Exception('保存出错');
        }
    }

    public static function getDefaultTemplate()
    {
        return self::find()->where(['template_score_card_id' => 0, 'parent_id' => 0])->asArray()->all();
    }

    public static function initRoleScoreCardTemplate($templateId)
    {
        $default = self::getDefaultTemplate();
        foreach ($default as $item) {
            $model = new self();
            $model->description = $item['description'];
            $model->sort = $item['sort'];
            $model->parent_id = 0;
            $model->is_part_title = $item['is_part_title'];
            $model->total_type = $item['total_type'];
            $model->template_score_card_id = $templateId;

            if (!$model->save()) {
                var_dump($model->getErrors());
                exit;
            }

            $cmodel = new self();
            $cmodel->description = "";
            $cmodel->parent_id = $model->id;
            $cmodel->sort = 0;
            $cmodel->template_score_card_id = $templateId;
            if (!$cmodel->save()) {
                var_dump($cmodel->getErrors());
                exit;
            }
        }
        return true;
    }

    /**
     * @param $templateId
     * @return array
     */
    public static function getDetailTree($templateId = 0)
    {
        if (!RoleScoreCardTemplate::findOne(['score_card_template_id' => $templateId])) {
            return [];
        }

        $detailList = self::find()
            ->where(['template_score_card_id' => $templateId])
            ->orderBy('sort asc')
            ->asArray()
            ->all();

        if (empty($detailList)) {
            self::initRoleScoreCardTemplate($templateId);
            return self::getDetailTree($templateId);
        }

        $list = toTree($detailList);

        foreach ($list as &$item){
            $item['score'] = 0;
            $item['tempScore'] = 0;
            $item['remarks'] = "";
            $item['tmpRemarks'] = "";
        }

        ArrayHelper::multisort($list, 'sort', SORT_ASC);
        return $list;
    }
}
