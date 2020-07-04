<?php

use mirocow\settings\models\Settings;
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m190818_222139_ofd_init
 */
class m190818_222139_ofd_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ofd_payment_agent_info', [
            'id' => $this->primaryKey(),
            'agent_type' => $this->string(50)->defaultValue('BANK_PAYMENT_AGENT'), // BANK_PAYMENT_AGENT
            'transfer_agent_phone' => $this->string(50),
            'transfer_agent_name' => $this->string(50),
            'transfer_agent_address' => $this->string(50),
            'transfer_agent_inn' => $this->string(50),
            'payment_agent_operation' => $this->string(50),
            'payment_agent_phone' => $this->string(50),
            'receiver_phone' => $this->string(50),
            'supplier_inn' => $this->string(50),
            'supplier_name' => $this->string(50),
            'supplier_phone' => $this->string(50),
            'create_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->timestamp()->null(),
        ]);

        $this->createTable('ofd_receipt_item', [
            'id' => $this->primaryKey(),
            'receipt_id' => $this->integer(11),
            'label' => $this->string(50),
            'price' => $this->float(15),
            'amount' => $this->float(15),
            'quantity' => $this->integer(5),
            /**
             * 3.1.1. Возможные значения вида вычисляемого НДС
             */
            'vat' => $this->string(20),
            'marking_code' => $this->string(50),
            'marking_code_tructured' => $this->string(50),
            /**
                1 – предоплата 100%;
                2 – предоплата;
                3 – аванс;
                4 – полный расчет;
                5 – частичный расчет;
                6 – передача в кредит;
                7 – оплата в кредит.
             */
            'payment_method' => $this->integer(2)->defaultValue(1)->comment('Признак способа расчета'),
            /**
             * https://ru.wikipedia.org/wiki/Общероссийский_классификатор_стран_мира
             */
            'origin_country_code' => $this->integer(3),
            'customs_declaration_number' => $this->string(20),
            /**
             * 3.1.4. Возможные значения признака предмета расчета
             * @see https://ofd.ru/razrabotchikam/ferma#возможные_значения_признака_предмета_расчета_поля_paymenttype
             */
            'payment_type' => $this->integer(2)->defaultValue(1)->comment('Способ оплаты.'), // 4
            'payment_agent_info_id' => $this->integer(11)->null()->comment('Данные платежного агента'),
            'create_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->timestamp()->null(),
        ]);

        $this->createTable('ofd_receipt', [
            'id' => $this->primaryKey(),
            'inn' => $this->string(12)->comment('ИНН лица, от имени которого генерируется кассовый документ (чек)'),
            /**
             * 3.1.2. Типы формируемых чеков
             */
            'type' => $this->string(20)->defaultValue('Income')->comment('Типы формируемых чеков'),
            'invoice' => $this->string(20)->comment('Идентификатор счета, на основании которого генерируется чек'),
            'id' => $this->primaryKey(),
            /**
             * 3.1.3. Возможные значения типа налогообложения
             * @see https://ofd.ru/razrabotchikam/ferma#возможные_значения_типа_налогообложения_поле_taxationsystem
             */
            'taxation_system' => $this->string(20)->defaultValue('Common')->comment('Система налогообложения'),
            'email' => $this->string(50),
            'phone' => $this->string(50),
            /**
             * 3.1.4. Возможные значения признака предмета расчета
             * @see https://ofd.ru/razrabotchikam/ferma#возможные_значения_признака_предмета_расчета_поля_paymenttype
             */
            'payment_type' => $this->integer(2)->defaultValue(1)->comment('Способ оплаты.'), // 4
            'custom_user_property' => $this->text(),
            'payment_agent_info_id' => $this->integer(11)->comment('Данные платежного агента'),
            'correction_info' => $this->text()->null()->comment('Корректирующие данные'),
            'client_info' => $this->text()->comment('Данные о покупателе'),
            'create_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->timestamp()->null(),
        ]);

        $this->createTable('ofd_receipt_status', [
            'id' => $this->primaryKey(),
            'receipt_id' => $this->integer(11),
            'receiptId' => $this->string(30)->null(),
            'status_code' => $this->integer(11),
            'status_name' => $this->string(20)->null(),
            'status_message' => $this->string(255)->null(),
            'modified_date_utc' => $this->timestamp()->null(),
            'receipt_date_utc' => $this->timestamp()->null(),
            'device_id' => $this->string(20)->null(),
            'rnm' => $this->string(20)->null(),
            'zn' => $this->string(20)->null(),
            'fn' => $this->string(20)->null(),
            'fdn' => $this->string(20)->null(),
            'fpd' => $this->string(20)->null(),
            'create_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->timestamp()->null(),
        ]);

        $this->addForeignKey('fk_ofd_receipt_item_receipt', '{{%ofd_receipt_item}}', 'receipt_id', '{{%ofd_receipt}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_ofd_receipt_status_receipt', '{{%ofd_receipt_status}}', 'receipt_id', '{{%ofd_receipt}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_ofd_receipt_item_agent_info', '{{%ofd_receipt_item}}', 'payment_agent_info_id', '{{%ofd_payment_agent_info}}', 'id', 'CASCADE', 'NO ACTION');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190818_222139_ofd_init cannot be reverted.\n";

        return false;
    }
}
