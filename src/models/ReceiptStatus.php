<?php

namespace mirocow\ofd\models;

use Yii;

/**
 * This is the model class for table "ofd_receipt_status".
 *
 * @property int $id
 * @property int $receipt_id
 * @property string $status_code
 * @property string $status_name
 * @property string $status_message
 * @property string $modified_date_utc
 * @property string $receipt_date_utc
 * @property string $device_id
 * @property string $rnm
 * @property string $zn
 * @property string $fn
 * @property string $fdn
 * @property string $fpd
 * @property string $create_at
 * @property string $update_at
 *
 * @property Receipt $receipt
 */
class ReceiptStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ofd_receipt_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['status_code', 'status_name', 'modified_date_utc', 'receipt_date_utc', 'device_id', 'rnm', 'zn', 'fn', 'fdn', 'fpd'], 'string', 'max' => 20],
            [['status_message'], 'string', 'max' => 255],
            [['receipt_id'], 'exist', 'skipOnError' => true, 'targetClass' => Receipt::class, 'targetAttribute' => ['receipt_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'receipt_id' => Yii::t('app', 'Receipt ID'),
            'status_code' => Yii::t('app', 'Status Code'),
            'status_name' => Yii::t('app', 'Status Name'),
            'status_message' => Yii::t('app', 'Status Message'),
            'modified_date_utc' => Yii::t('app', 'Modified Date Utc'),
            'receipt_date_utc' => Yii::t('app', 'Receipt Date Utc'),
            'device_id' => Yii::t('app', 'Device ID'),
            'rnm' => Yii::t('app', 'Rnm'),
            'zn' => Yii::t('app', 'Zn'),
            'fn' => Yii::t('app', 'Fn'),
            'fdn' => Yii::t('app', 'Fdn'),
            'fpd' => Yii::t('app', 'Fpd'),
            'create_at' => Yii::t('app', 'Create At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceipt()
    {
        return $this->hasOne(Receipt::class, ['id' => 'receipt_id']);
    }
}