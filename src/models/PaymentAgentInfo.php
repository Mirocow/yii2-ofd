<?php

namespace mirocow\ofd\models;

use Yii;

/**
 * This is the model class for table "ofd_payment_agent_info".
 *
 * @property int $id
 * @property string $agent_type
 * @property string $transfer_agent_phone
 * @property string $transfer_agent_name
 * @property string $transfer_agent_address
 * @property string $transfer_agent_inn
 * @property string $payment_agent_operation
 * @property string $payment_agent_phone
 * @property string $receiver_phone
 * @property string $supplier_inn
 * @property string $supplier_name
 * @property string $supplier_phone
 * @property string $create_at
 * @property string $update_at
 *
 * @property OfdCustomer[] $ofdCustomers
 * @property OfdReceiptItem[] $ofdReceiptItems
 */
class PaymentAgentInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ofd_payment_agent_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_at', 'update_at'], 'safe'],
            [['agent_type', 'transfer_agent_phone', 'transfer_agent_name', 'transfer_agent_address', 'transfer_agent_inn', 'payment_agent_operation', 'payment_agent_phone', 'receiver_phone', 'supplier_inn', 'supplier_name', 'supplier_phone'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'agent_type' => Yii::t('app', 'Agent Type'),
            'transfer_agent_phone' => Yii::t('app', 'Transfer Agent Phone'),
            'transfer_agent_name' => Yii::t('app', 'Transfer Agent Name'),
            'transfer_agent_address' => Yii::t('app', 'Transfer Agent Address'),
            'transfer_agent_inn' => Yii::t('app', 'Transfer Agent Inn'),
            'payment_agent_operation' => Yii::t('app', 'Payment Agent Operation'),
            'payment_agent_phone' => Yii::t('app', 'Payment Agent Phone'),
            'receiver_phone' => Yii::t('app', 'Receiver Phone'),
            'supplier_inn' => Yii::t('app', 'Supplier Inn'),
            'supplier_name' => Yii::t('app', 'Supplier Name'),
            'supplier_phone' => Yii::t('app', 'Supplier Phone'),
            'create_at' => Yii::t('app', 'Create At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfdCustomers()
    {
        return $this->hasMany(OfdCustomer::class, ['payment_agent_info_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfdReceiptItems()
    {
        return $this->hasMany(OfdReceiptItem::class, ['payment_agent_info_id' => 'id']);
    }
}
