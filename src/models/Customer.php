<?php

namespace mirocow\ofd\models;

use Yii;

/**
 * This is the model class for table "ofd_customer".
 *
 * @property int $id
 * @property int $user_id Покупатель для которого формируется чек
 * @property string $taxation_system Система налогообложения
 * @property string $email
 * @property string $phone
 * @property int $payment_type Способ оплаты.
 * @property string $custom_user_property
 * @property int $payment_agent_info_id Данные платежного агента
 * @property string $correction_info Корректирующие данные
 * @property string $client_info Данные о покупателе
 * @property string $create_at
 * @property string $update_at
 *
 * @property OfdPaymentAgentInfo $paymentAgentInfo
 * @property OfdReceipt[] $ofdReceipts
 * @property OfdReceiptItem[] $ofdReceiptItems
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ofd_customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_type', 'payment_agent_info_id'], 'integer'],
            [['custom_user_property', 'correction_info', 'client_info'], 'string'],
            [['create_at', 'update_at'], 'safe'],
            [['taxation_system'], 'string', 'max' => 20],
            [['email', 'phone'], 'string', 'max' => 50],
            [['payment_agent_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => OfdPaymentAgentInfo::class, 'targetAttribute' => ['payment_agent_info_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'taxation_system' => Yii::t('app', 'Taxation System'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'custom_user_property' => Yii::t('app', 'Custom User Property'),
            'payment_agent_info_id' => Yii::t('app', 'Payment Agent Info ID'),
            'correction_info' => Yii::t('app', 'Correction Info'),
            'client_info' => Yii::t('app', 'Client Info'),
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
    public function getOfdReceipts()
    {
        return $this->hasMany(OfdReceipt::class, ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfdReceiptItems()
    {
        return $this->hasMany(OfdReceiptItem::class, ['ofd_customer_id' => 'id']);
    }
}
