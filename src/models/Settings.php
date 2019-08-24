<?php

namespace mirocow\ofd\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;

class Settings extends Model
{
    private $inn;
    private $type;
    private $email;
    private $taxsystem;
    private $tax;
    private $paymentMethod;
    private $paymentType;
    private $paymentItemsPaymentType;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inn'], 'string', 'max' => 12],
            [['type', 'taxSystem', 'tax', 'paymentMethod', 'paymentType', 'paymentItemsPaymentType'], 'safe'],
            [['email'], 'email'],
            [['inn'], 'validateInn'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inn' => Yii::t('app', 'Inn'),
            'type' => Yii::t('app', 'Type'),
            'email' => Yii::t('app', 'Email'),
            'taxSystem' => Yii::t('app', 'Tax System'),
            'tax' => Yii::t('app', 'Tax'),
            'paymentMethod' => Yii::t('app', 'Payment Method'),
            'paymentType' => Yii::t('app', 'Payment Type'),
            'paymentItemsPaymentType' => Yii::t('app', 'Payment Items Payment Type'),
        ];
    }

    /**
     * @param $attribute
     *
     * @return bool
     */
    public function validateInn($attribute) {
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

    public function save()
    {
        try {

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'inn',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->inn;
            $model->save();

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'type',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->type;
            $model->save();

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'email',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->email;
            $model->save();

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'tax_system',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->taxsystem;
            $model->save();

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'tax',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->tax;
            $model->save();

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'payment_method',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->paymentMethod;
            $model->save();

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'payment_type',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->paymentType;
            $model->save();

            $model = \mirocow\settings\models\Settings::find()->where([
                'key' => 'payment_items.payment_type',
                'group_name' => 'ofd',
            ])->one();
            $model->value = $this->paymentItemsPaymentType;
            $model->save();

        } catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @return mixed
     */
    public function getInn()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'inn', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setInn($value)
    {
        $this->inn = $value;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'type', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setType($value)
    {
        $this->type = $value;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'email', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setEmail($value)
    {
        $this->email = $value;
    }

    /**
     * @return mixed
     */
    public function getTaxSystem()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'tax_system', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setTaxSystem($value)
    {
        $this->taxsystem = $value;
    }

    /**
     * @return mixed
     */
    public function getTax()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'tax', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setTax($value)
    {
        $this->tax = $value;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'payment_method', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setPaymentMethod($value)
    {
        $this->paymentMethod = $value;
    }

    /**
     * @return mixed
     */
    public function getPaymentType()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'payment_type', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setPaymentType($value)
    {
        $this->paymentType = $value;
    }

    /**
     * @return mixed
     */
    public function getPaymentItemsPaymentType()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'payment_items.payment_type', 'group_name' => 'ofd'])->one();
        return $model->value;
    }

    /**
     * @param $value
     */
    public function setPaymentItemsPaymentType($value)
    {
        $this->paymentItemsPaymentType = $value;
    }

}