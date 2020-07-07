<?php

namespace mirocow\ofd\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "ofd_receipt".
 *
 * @property string $inn ИНН лица, от имени которого генерируется кассовый документ (чек)
 * @property string $type Тип формируемого документа
 * @property string $invoice Идентификатор счета, на основании которого генерируется чек
 * @property string $email
 * @property string $phone
 * @property string $created_at
 *
 */
class Receipt extends Model
{
    /** @var ReceiptItem[] */
    private $items;

    public $inn;
    public $invoice;
    public $type;
    public $email;
    public $phone;
    public $created_at;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_at'], 'safe'],
            [['inn'], 'string', 'max' => 12],
            [['inn'], 'validateInn'],
            [['invoice', 'type'], 'string', 'max' => 20],
            [['email', 'phone'], 'string', 'max' => 50],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inn' => Yii::t('app', 'Inn'),
            'invoice' => Yii::t('app', 'Invoice'),
            'type' => Yii::t('app', 'Type'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'created_at' => Yii::t('app', 'Created'),
        ];
    }

    /**
     * @param $attribute
     *
     * @return bool
     */
    public function validateInn($attribute)
    {
        $result = false;
        $inn = (string) $this->{$attribute};
        if (!$inn) {
            $error_code = 1;
            $this->addError($attribute, 'ИНН пуст');
        } elseif (preg_match('/[^0-9]/', $inn)) {
            $error_code = 2;
            $this->addError($attribute, 'ИНН может состоять только из цифр');
        } elseif (!in_array($inn_length = strlen($inn), [10, 12])) {
            $error_code = 3;
            $this->addError($attribute, 'ИНН может состоять только из 10 или 12 цифр');
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
                $this->addError($attribute, 'Неправильное контрольное число');
            }
        }
        return $result;
    }

    /**
     * @param ReceiptItem $item
     */
    public function addItem( $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
