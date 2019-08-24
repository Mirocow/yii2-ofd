<?php

namespace mirocow\ofd\models;

use Yii;

/**
 * This is the model class for table "ofd_receipt".
 *
 * @property int $id
 * @property string $inn ИНН лица, от имени которого генерируется кассовый документ (чек)
 * @property string $type Тип формируемого документа
 * @property string $invoice Идентификатор счета, на основании которого генерируется чек
 * @property string $create_at
 * @property string $update_at
 *
 * @property OfdCustomer $customer
 */
class Receipt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ofd_receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_at', 'update_at'], 'safe'],
            [['inn'], 'string', 'max' => 12],
            [['type', 'invoice'], 'string', 'max' => 20],
            [['payment_type', 'payment_agent_info_id'], 'integer'],
            [['custom_user_property', 'correction_info', 'client_info'], 'string'],
            [['create_at', 'update_at'], 'safe'],
            [['taxation_system'], 'string', 'max' => 20],
            [['email', 'phone'], 'string', 'max' => 50],
            [['payment_agent_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentAgentInfo::class, 'targetAttribute' => ['payment_agent_info_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inn' => Yii::t('app', 'Inn'),
            'type' => Yii::t('app', 'Type'),
            'invoice' => Yii::t('app', 'Invoice'),
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
    public function getItems()
    {
        return $this->hasMany(ReceiptItem::class, ['receipt_id' => 'id']);
    }
}
