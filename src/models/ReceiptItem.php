<?php

namespace mirocow\ofd\models;

use Yii;

/**
 * This is the model class for table "ofd_receipt_item".
 *
 * @property int $id
 * @property int $ofd_customer_id
 * @property string $label
 * @property double $price
 * @property double $amount
 * @property int $quantity
 * @property string $vat
 * @property string $marking_code
 * @property string $marking_code_tructured
 * @property int $payment_method Признак способа расчета
 * @property int $origin_country_code
 * @property string $customs_declaration_number
 * @property int $payment_type Способ оплаты.
 * @property int $payment_agent_info_id Данные платежного агента
 * @property string $create_at
 * @property string $update_at
 *
 * @property OfdPaymentAgentInfo $paymentAgentInfo
 * @property OfdCustomer $ofdCustomer
 */
class ReceiptItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ofd_receipt_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ofd_customer_id', 'quantity', 'payment_method', 'origin_country_code', 'payment_type', 'payment_agent_info_id'], 'integer'],
            [['price', 'amount'], 'number'],
            [['create_at', 'update_at'], 'safe'],
            [['label', 'marking_code', 'marking_code_tructured'], 'string', 'max' => 50],
            [['vat', 'customs_declaration_number'], 'string', 'max' => 20],
            [['payment_agent_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => OfdPaymentAgentInfo::class, 'targetAttribute' => ['payment_agent_info_id' => 'id']],
            [['ofd_customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => OfdCustomer::class, 'targetAttribute' => ['ofd_customer_id' => 'id']],
            [['receipt_id'], 'exist', 'skipOnError' => true, 'targetClass' => OfdReceipt::class, 'targetAttribute' => ['receipt_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ofd_customer_id' => Yii::t('app', 'Ofd Customer ID'),
            'label' => Yii::t('app', 'Label'),
            'price' => Yii::t('app', 'Price'),
            'amount' => Yii::t('app', 'Amount'),
            'quantity' => Yii::t('app', 'Quantity'),
            'vat' => Yii::t('app', 'Vat'),
            'marking_code' => Yii::t('app', 'Marking Code'),
            'marking_code_tructured' => Yii::t('app', 'Marking Code Tructured'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'origin_country_code' => Yii::t('app', 'Origin Country Code'),
            'customs_declaration_number' => Yii::t('app', 'Customs Declaration Number'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'payment_agent_info_id' => Yii::t('app', 'Payment Agent Info ID'),
            'create_at' => Yii::t('app', 'Create At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentAgentInfo()
    {
        return $this->hasOne(OfdPaymentAgentInfo::class, ['id' => 'payment_agent_info_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfdCustomer()
    {
        return $this->hasOne(OfdCustomer::class, ['id' => 'ofd_customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceipt()
    {
        return $this->hasOne(OfdReceipt::class, ['id' => 'receipt_id']);
    }
}
