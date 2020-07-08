<?php

namespace mirocow\ofd\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "ofd_receipt_status".
 *
 * @property string $invoice
 * @property string $type
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
            [['create_at', 'update_at'], 'safe'],
            [['status_code', 'status_name', 'modified_date_utc', 'receipt_date_utc', 'device_id', 'rnm', 'zn', 'fn', 'fdn', 'fpd'], 'string', 'max' => 20],
            [['invoice', 'type'], 'string', 'max' => 20],
            [['status_message'], 'string', 'max' => 255],
            [['receiptId'], 'string', 'max' => 36],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_at',
                'updatedAtAttribute' => 'update_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'receiptId' => Yii::t('app', 'Receipt ID'),
            'invoice' => Yii::t('app', 'Invoice'),
            'type' => Yii::t('app', 'Type'),
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
}