<?php

use mirocow\settings\models\Settings;
use yii\db\Migration;

/**
 * Class m200704_193213_add_ofd_settings
 */
class m200704_193213_add_ofd_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $id = Settings::find()
            ->max('id');

        $id++;

        if(!Settings::find()->where(['key' => 'inn', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'inn',
                'name'  => 'ИНН организации',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!Settings::find()->where(['key' => 'type', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'type',
                'name'  => 'Тип формируемого документа',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!Settings::find()->where(['key' => 'email', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'email',
                'name'  => 'Адрес e-mail для уведомлений',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!Settings::find()->where(['key' => 'tax_system', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'tax_system',
                'name'  => 'Система налогообложения',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!Settings::find()->where(['key' => 'tax', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'tax',
                'name'  => 'Ставка НДС по умолчанию',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!Settings::find()->where(['key' => 'payment_method', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'payment_method',
                'name'  => 'Признак способа расчета',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!Settings::find()->where(['key' => 'payment_type', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'payment_type',
                'name'  => 'Признак предмета расчета',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!Settings::find()->where(['key' => 'payment_items.payment_type', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'payment_items.payment_type',
                'name'  => 'Типы оплат',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200704_193213_add_ofd_settings cannot be reverted.\n";

        return false;
    }
}
