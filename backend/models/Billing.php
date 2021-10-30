<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%service_billing}}".
 *
 * @property int $id
 * @property int $company_id 公司ID
 * @property int $time_sheet 时间表
 * @property string $on_site_allowance 现场津贴
 * @property array $billed_amount_detail 账单金额明细
 * @property int $is_approved 是否已审核；0：没有；1；已审核
 * @property int $team_id 团队ID
 * @property string $billed_amount 账单金额
 * @property string $year
 * @property int $month
 * @property int $real_month
 * @property string $commission
 * @property string $date
 * @property string $invoice_number
 * @property string $invoice_date
 * @property string $approved_at 审核时间
 */
class Billing extends \yii\db\ActiveRecord
{
    const BILLING_AMOUNT = [
        ["item" => "Reimbursement", "description" => '', "price" => 0],
        ["item" => "Fee", "description" => '', "price" => 0],
        ["item" => "Out of Pocket", "description" => '', "price" => 0],
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%service_billing}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'time_sheet', 'is_approved', 'team_id', 'month', 'real_month'], 'integer'],
            [['on_site_allowance', 'billed_amount','commission'], 'number'],
            [['billed_amount_detail', 'billed_amount'], 'required'],
            [['billed_amount_detail', 'year', 'date', 'approved_at'], 'safe'],
            [['invoice_number', 'invoice_date'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'time_sheet' => 'Time Sheet',
            'on_site_allowance' => 'On Site Allowance',
            'billed_amount_detail' => 'Billed Amount Detail',
            'is_approved' => 'Is Approved',
            'team_id' => 'Team ID',
            'billed_amount' => 'Billed Amount',
            'year' => 'Year',
            'month' => 'Month',
            'date' => 'Date',
            'invoice_number' => 'Invoice Number',
            'invoice_date' => 'Invoice Date',
            'approved_at' => 'Approved At',
        ];
    }
}
