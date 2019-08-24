<?php

use mirocow\settings\widgets\DropDownField;
use mirocow\settings\widgets\StringField;
use mirocow\seo\models\Meta;
use mirocow\seo\Module;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model mirocow\seo\models\Meta */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="meta-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'inn')->widget(StringField::class, [])?>

        <?= $form->field($model, 'type')->widget(DropDownField::class, [
            'items' => \mirocow\ofd\helpers\SettingsHelper::typeList()
        ])?>

        <?= $form->field($model, 'email')->widget(StringField::class, [])?>

        <?= $form->field($model, 'taxSystem')->widget(DropDownField::class, [
            'items' => \mirocow\ofd\helpers\SettingsHelper::taxSystemList(),
        ])?>

        <?= $form->field($model, 'tax')->widget(DropDownField::class, [
            'items' => \mirocow\ofd\helpers\SettingsHelper::taxList(),
        ])?>

        <?= $form->field($model, 'paymentMethod')->widget(DropDownField::class, [
            'items' => \mirocow\ofd\helpers\SettingsHelper::paymentMethodList(),
        ])?>

        <?= $form->field($model, 'paymentType')->widget(DropDownField::class, [
            'items' => \mirocow\ofd\helpers\SettingsHelper::PaymentTypeList(),
        ])?>

        <?= $form->field($model, 'paymentItemsPaymentType')->widget(DropDownField::class, [
            'items' => \mirocow\ofd\helpers\SettingsHelper::PaymentItemsPaymentTypeList(),
        ])?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

