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
 * @property int $customer_id Содержимое клиентского чека
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
            [['customer_id'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['inn'], 'string', 'max' => 12],
            [['inn'], 'validateInn', 'message' => ''],
            [['type', 'invoice'], 'string', 'max' => 20],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => OfdCustomer::class, 'targetAttribute' => ['customer_id' => 'id']],
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
            'customer_id' => Yii::t('app', 'Customer ID'),
            'create_at' => Yii::t('app', 'Create At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }

    public static function validateInn($model, $attribute) {
        $result = false;
        $inn = (string) $model->{$attribute};
        if (!$inn) {
            $error_code = 1;
            $error_message = 'ИНН пуст';
        } elseif (preg_match('/[^0-9]/', $inn)) {
            $error_code = 2;
            $error_message = 'ИНН может состоять только из цифр';
        } elseif (!in_array($inn_length = strlen($inn), [10, 12])) {
            $error_code = 3;
            $error_message = 'ИНН может состоять только из 10 или 12 цифр';
        } else {
            $check_digit = function($inn, $coefficients) {
                $n = 0;
                foreach ($coefficients as $i => $k) {
                    $n += $k * (int) $inn{$i};
                }
                return $n % 11 % 10;
            };
            switch ($inn_length) {
                case 10:
                    $n10 = $check_digit($inn, [2, 4, 10, 3, 5, 9, 4, 6, 8]);
                    if ($n10 === (int) $inn{9}) {
                        $result = true;
                    }
                break;
                case 12:
                    $n11 = $check_digit($inn, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
                    $n12 = $check_digit($inn, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
                    if (($n11 === (int) $inn{10}) && ($n12 === (int) $inn{11})) {
                        $result = true;
                    }
                break;
            }
            if (!$result) {
                $error_code = 4;
                $error_message = 'Неправильное контрольное число';
            }
        }
        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(OfdCustomer::class, ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(OfdReceiptItem::class, ['receipt_id' => 'id']);
    }
}
