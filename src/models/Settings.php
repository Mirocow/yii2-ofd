<?php

namespace mirocow\ofd\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * Class Settings
 * @package mirocow\ofd\models
 */
class Settings extends Model
{
    private $bankname;
    private $bik;
    private $correspondentaccount;
    private $invoicenumber;
    private $inn;
    private $email;
    private $phone;
    private $taxSystem;
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
            [['taxSystem', 'tax', 'paymentMethod', 'paymentType', 'paymentItemsPaymentType'], 'safe'],
            [['email'], 'email'],
            [['bankname', 'bik', 'correspondentaccount', 'invoicenumber', 'kpp', 'organisationname', 'organisationaddress'], 'string'],
            [['phone'], 'string', 'max' => 14],
            [['inn'], 'validateInn'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bankname' => Yii::t('app', 'Bank name'),
            'bik' => Yii::t('app', 'BIK'),
            'correspondentaccount' => Yii::t('app', 'Correspondent account'),
            'invoicenumber' => Yii::t('app', 'Invoice number'),
            'inn' => Yii::t('app', 'INN'),
            'kpp' => Yii::t('app', 'KPP'),
            'organisationname' => Yii::t('app', 'Organisation name'),
            'organisationaddress' => Yii::t('app', 'Organisation address'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
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

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function save()
    {
        try {
            foreach (array_keys($this->attributeLabels()) as $key){
                $this->setingsSave($key, $this->{$key});
            }
        } catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @param $key
     * @param string $value
     * @param string $group_name
     *
     * @throws \yii\base\Exception
     */
    public function setingsSave($key, $value = '', $group_name = 'ofd')
    {
        $model = \mirocow\settings\models\Settings::find()->where([
            'key' => $key,
            'group_name' => $group_name,
        ])->one();
        if(!$model){
            $model = new \mirocow\settings\models\Settings();
            $model->type = 1;
            $model->key = $key;
            $model->group_name = $group_name;
        }
        $model->value = $value;
        if(!$model->save()){
            throw new \yii\base\Exception();
        }
    }

    /**
     * @return mixed
     */
    public function getBankName()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'bankname', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setBankName($value)
    {
        $this->bankname = $value;
    }

    /**
     * @return mixed
     */
    public function getBik()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'bik', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setBik($value)
    {
        $this->bik = $value;
    }

    /**
     * @return mixed
     */
    public function getCorrespondentAccount()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'correspondentaccount', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setCorrespondentAccount($value)
    {
        $this->correspondentaccount = $value;
    }

    /**
     * @return mixed
     */
    public function getInvoiceNumber()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'invoicenumber', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setInvoiceNumber($value)
    {
        $this->invoicenumber = $value;
    }

    /**
     * @return mixed
     */
    public function getKPP()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'kpp', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setKPP($value)
    {
        $this->kpp = $value;
    }

    /**
     * @return mixed
     */
    public function getOrganisationName()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'organisationname', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setOrganisationName($value)
    {
        $this->organisationname = $value;
    }

    /**
     * @return mixed
     */
    public function getOrganisationAddress()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'organisationaddress', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setOrganisationAddress($value)
    {
        $this->organisationaddress = $value;
    }

    /**
     * @return mixed
     */
    public function getInn()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'inn', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
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
    public function getEmail()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'email', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
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
    public function getPhone()
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'phone', 'group_name' => 'ofd'])->one();
        return $model->value ?? '';
    }

    /**
     * @param $value
     */
    public function setPhone($value)
    {
        $this->phone = $value;
    }

    /**
     * @return mixed
     */
    public function getTaxSystem($defaultValue = 'Common')
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'taxSystem', 'group_name' => 'ofd'])->one();
        return $model->value ?? $defaultValue;
    }

    /**
     * @param $value
     */
    public function setTaxSystem($value)
    {
        $this->taxSystem = $value;
    }

    /**
     * @return mixed
     */
    public function getTax($defaultValue = 'vat0')
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'tax', 'group_name' => 'ofd'])->one();
        return $model->value ?? $defaultValue;
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
    public function getPaymentMethod($defaultValue = 1)
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'paymentMethod', 'group_name' => 'ofd'])->one();
        return $model->value ?? $defaultValue;
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
    public function getPaymentType($defaultValue = 4)
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'paymentType', 'group_name' => 'ofd'])->one();
        return $model->value ?? $defaultValue;
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
    public function getPaymentItemsPaymentType($defaultValue = 1)
    {
        $model = \mirocow\settings\models\Settings::find()
            ->where(['key'   => 'paymentItemsPaymentType', 'group_name' => 'ofd'])->one();
        return $model->value ?? $defaultValue;
    }

    /**
     * @param $value
     */
    public function setPaymentItemsPaymentType($value)
    {
        $this->paymentItemsPaymentType = $value;
    }

}