<?php

namespace mirocow\ofd\helpers;

use Yii;

class SettingsHelper
{
    /**
     * Система налогообложения, см. п. 3.1.3
     * @see https://ofd.ru/razrabotchikam/ferma#возможные_значения_типа_налогообложения_поле_taxationsystem
     * @return array
     */
    static function taxSystemList() {
        return [
            '0' => Yii::t('app', 'Common'),
            '1' => Yii::t('app', 'Simple In'),
            '2' => Yii::t('app', 'Simple In-Out'),
            '3' => Yii::t('app', 'Unified'),
            '4' => Yii::t('app', 'Unified Agricultural'),
            '5' => Yii::t('app', 'Patent'),
        ];
    }

    /**
     * Вид вычисляемого НДС см. п 3.1.1
     * @see https://ofd.ru/razrabotchikam/ferma#возможные_значения_вида_вычисляемого_ндс_поле_vat
     * @return array
     */
    static function taxList() {
        return [
            'Vat0' => Yii::t('app', 'VAT Free'),
            'Vat10' => Yii::t('app', 'VAT 10%'),
            'Vat18' => Yii::t('app', 'VAT 18%'),
            'Vat20' => Yii::t('app', 'VAT 20%'),
            'CalculatedVat10110' => Yii::t('app', 'Calculated VAT 10/110'),
            'CalculatedVat18118' => Yii::t('app', 'Calculated VAT 18/118'),
            'CalculatedVat20120' => Yii::t('app', 'Calculated VAT 20/120'),
        ];
    }

    /**
     * Тип формируемого документа (чек), см. п. 3.1.2
     * @see https://ofd.ru/razrabotchikam/ferma#типы_формируемых_чеков
     * @return array
     */
    static function typeList() {
        return [
            'Income' => Yii::t('app', 'Income'),
            'IncomeReturn' => Yii::t('app', 'Income Return'),
            'IncomeCorrection' => Yii::t('app', 'Income Correction'),
            'Expense' => Yii::t('app', 'Expense'),
            'ExpenseReturn' => Yii::t('app', 'Expense Return'),
        ];
    }

    /**
     * Признак способа расчета
     * @return array
     */
    static function paymentMethodList()
    {
        return [
            '1' => Yii::t('app', '100% prepayment'),
            '2' => Yii::t('app', 'Prepayment'),
            '3' => Yii::t('app', 'Advance payment'),
            '4' => Yii::t('app', 'Full calculation'),
            '5' => Yii::t('app', 'Partial calculation'),
            '6' => Yii::t('app', 'Transfer on credit'),
            '7' => Yii::t('app', 'Payment on credit'),
        ];
    }

    /**
     * Признак предмета расчета для всего чека см. п. 3.1.4
     * @see https://ofd.ru/razrabotchikam/ferma#возможные_значения_признака_предмета_расчета_поля_paymenttype
     * @return array
     */
    static function PaymentTypeList()
    {
        return [
            '1' => Yii::t('app', 'ТОВАР или Т'),
            '4' => Yii::t('app', 'УСЛУГА или У'),
        ];
    }

    /**
     * Тип оплаты
     * @return array
     */
    static function PaymentItemsPaymentTypeList()
    {
        return [
            '0' => Yii::t('app', 'In Cash'),
            '1' => Yii::t('app', 'Non Cash'),
        ];
    }

}