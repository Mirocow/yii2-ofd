<?php

namespace mirocow\ofd\models;

use Yii;
use yii\base\Model;

/**
 * @property string $label
 * @property double $price
 * @property double $amount
 * @property int $quantity
 * @property string $vat
 *
 */
class ReceiptItem extends Model
{

    public $label;
    public $price;
    public $amount;
    public $quantity;
    public $vat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price', 'amount', 'quantity'], 'number'],
            [['label'], 'string', 'max' => 50],
            [['vat'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'label' => Yii::t('app', 'Label'),
            'price' => Yii::t('app', 'Price'),
            'amount' => Yii::t('app', 'Amount'),
            'quantity' => Yii::t('app', 'Quantity'),
            'vat' => Yii::t('app', 'Vat'),
        ];
    }
}
