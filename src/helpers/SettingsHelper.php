<?php

namespace mirocow\ofd\helpers;

use Yii;

class SettingsHelper
{
    function tax_system_list() {
        return [
            '0' => Yii::t('app', 'Common'),
            '1' => Yii::t('app', 'Simple In'),
            '2' => Yii::t('app', 'Simple In-Out'),
            '3' => Yii::t('app', 'Unified'),
            '4' => Yii::t('app', 'Unified Agricultural'),
            '5' => Yii::t('app', 'Patent'),
        ];
    }

    function tax_list() {
        return [
            'Vat0' => Yii::t('app', 'VAT Free'),
            'Vat10' => Yii::t('app', 'VAT 10%'),
            'Vat18' => Yii::t('app', 'VAT 18%'),
            'CalculatedVat10110' => Yii::t('app', 'Calculated VAT 10/110'),
            'CalculatedVat18118' => Yii::t('app', 'Calculated VAT 18/118'),
        ];
    }

    function type_list() {
        return [
            'Income' => Yii::t('app', 'Income'),
            'IncomeReturn' => Yii::t('app', 'Income Return'),
            'Expense' => Yii::t('app', 'Expense'),
            'ExpenseReturn' => Yii::t('app', 'Expense Return'),
        ];
    }

    function status_list() {
        return [
            0 => Yii::t('app', 'New'),
            1 => Yii::t('app', 'Queued'),
            2 => Yii::t('app', 'Fiscalized'),
            3 => Yii::t('app', 'Processed'),
        ];
    }
}