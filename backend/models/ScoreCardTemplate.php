<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "co_service_score_card_template".
 *
 * @property int $id
 * @property int $ranking_id
 * @property array $detail 积分卡模板详情
 * @property string $created_at 创建时间
 * @property string $updated_at 上次更新时间
 */
class ScoreCardTemplate extends \yii\db\ActiveRecord
{
    const PART_TITLE_TYPE = 1;  //分区头部
    const ITEM_TITLE_TYPE = 2;  //单项
    const TOTAL_A = 3; //汇总A
    const TOTAL_B = 4; //汇总B
    const TOTAL_A_B = 5; //汇总A+B

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'co_service_score_card_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['detail', 'created_at'], 'required'],
            ['ranking_id', 'integer'],
            [['detail', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'detail' => 'Detail',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'ranking_id' => 'Ranking ID',
        ];
    }

    public static function getDefaultData()
    {

        return [
            [

                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "type" => self::PART_TITLE_TYPE,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "PERFORMANCE SCORE (60%)",
            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "Given task deliver within deadlines\n    - Job started -  4 weeks\n    - Job completed – 8 weeks (Management account)",
                    ]
                ],
                "type" => self::ITEM_TITLE_TYPE,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "Team member management (Target)",

            ],
            [
                "score" => 0,
                "remarks" => "",
                "type" => self::ITEM_TITLE_TYPE,
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "Given task deliver within deadlines",


            ],
            [
                "score" => 0,
                "remarks" => "",
                "type" => self::ITEM_TITLE_TYPE,
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "To ensure all issue being cover in review note, suggest for improvement",


            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "11111",
                    ]
                ],

                "tempScore" => 0,
                "tmpRemarks" => "",
                "type" => self::ITEM_TITLE_TYPE,
                "description" => "Prepare report & updates for manager (Job status tracking, before Tuesday 12pm)",

            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "tempScore" => 0,
                "type" => self::ITEM_TITLE_TYPE,
                "tmpRemarks" => "",
                "description" => "Client management (Complains or retention)",

            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "tempScore" => 0,
                "type" => self::ITEM_TITLE_TYPE,
                "tmpRemarks" => "",
                "description" => "Client management (Complains or retention)",

            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "type" => self::TOTAL_A,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "TOTAL A",


            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "tempScore" => 0,
                "type" => self::PART_TITLE_TYPE,
                "tmpRemarks" => "",
                "description" => "CULTURAL SCORE (40%)",
            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "  - Template\n    - SOP\n    - Knowledge video\n    - FB post (At least 2 with approval)",
                    ]
                ],
                "type" => self::ITEM_TITLE_TYPE,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "Knowledge based (20%)",

            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "  - Presentation\n    - Sharing of knowledge",
                    ]
                ],
                "type" => self::ITEM_TITLE_TYPE,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "Self- Development (10%)",


            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "     - Supportive spiritual (Includes Punctuality, accept challenge, strategy) – 5%    - FB testimonial (at least one) – 5%",
                    ]
                ],
                "type" => self::ITEM_TITLE_TYPE,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "Customer Satisfactory (10%)",


            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "type" => self::TOTAL_B,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "TOTAL B",


            ],
            [
                "score" => 0,
                "remarks" => "",
                "children" => [
                    [
                        "children" => [],
                        "description" => "",
                    ]
                ],
                "type" => self::TOTAL_A_B,
                "tempScore" => 0,
                "tmpRemarks" => "",
                "description" => "TOTAL A + B",


            ]
        ];
    }

    /**
     * 获取模板
     * @param int $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getScoreCardTemplate($id = 0)
    {
        $scoreCardTemplateInfo = self::find()->where(['ranking_id' => $id])->asArray()->one();
        if (!$scoreCardTemplateInfo) {
            $scoreCardTemplateInfo = [
                'id' => 0,
                'score_detail' => self::getDefaultData()
            ];
        } else {
            $scoreCardTemplateInfo['score_detail'] = Json::decode($scoreCardTemplateInfo['detail'], true);
        }

        return $scoreCardTemplateInfo;
    }

}
