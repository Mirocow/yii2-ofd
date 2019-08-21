<?php

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
        if(!\settings\models\Settings::find()->where(['key' => 'ofd', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'ofd.inn',
                'name'  => 'Наименование банка',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

        if(!\settings\models\Settings::find()->where(['key' => 'ofd', 'group_name' => 'ofd'])->exists()) {
            $this->insert(Settings::tableName(), [
                'id'    => $id,
                'key'   => 'ofd.type',
                'name'  => 'Наименование банка',
                'value' => '',
                'type'  => Settings::TYPE_STRING,
                'group_name' => 'ofd',
            ]);
            $id++;
        }

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

        $this->createTable('ofd_customer', [
            'id' => $this->primaryKey(),
            'taxation_system' => $this->string(20)->defaultValue('Common')->comment('Система налогообложения'),
            'email' => $this->string(50),
            'phone' => $this->string(50),
            /**
             * @see https://ofd.ru/razrabotchikam/ferma#%D0%B2%D0%BE%D0%B7%D0%BC%D0%BE%D0%B6%D0%BD%D1%8B%D0%B5_%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D1%8F_%D0%BF%D1%80%D0%B8%D0%B7%D0%BD%D0%B0%D0%BA%D0%B0_%D0%BF%D1%80%D0%B5%D0%B4%D0%BC%D0%B5%D1%82%D0%B0_%D1%80%D0%B0%D1%81%D1%87%D0%B5%D1%82%D0%B0_%D0%BF%D0%BE%D0%BB%D1%8F_paymenttype
             */
            'payment_type' => $this->integer(2)->defaultValue(1)->comment('Способ оплаты.'), // 4
            'custom_user_property' => $this->text(),
            'payment_agent_info_id' => $this->integer(11)->comment('Данные платежного агента'),
            'correction_info' => $this->text()->null()->comment('Корректирующие данные'),
            'client_info' => $this->text()->comment('Данные о покупателе'),
            'create_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->timestamp()->null(),
        ]);

        // Items
        $this->createTable('ofd_receipt_item', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->null(),
            'receipt_id' => $this->integer(11),
            'label' => $this->string(50),
            'price' => $this->float(15),
            'amount' => $this->float(15),
            'quantity' => $this->integer(5),
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
            'origin_country_code' => $this->integer(3),
            'customs_declaration_number' => $this->string(20),
            /**
             * @see https://ofd.ru/razrabotchikam/ferma#%D0%B2%D0%BE%D0%B7%D0%BC%D0%BE%D0%B6%D0%BD%D1%8B%D0%B5_%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D1%8F_%D0%BF%D1%80%D0%B8%D0%B7%D0%BD%D0%B0%D0%BA%D0%B0_%D0%BF%D1%80%D0%B5%D0%B4%D0%BC%D0%B5%D1%82%D0%B0_%D1%80%D0%B0%D1%81%D1%87%D0%B5%D1%82%D0%B0_%D0%BF%D0%BE%D0%BB%D1%8F_paymenttype
             */
            'payment_type' => $this->integer(2)->defaultValue(1)->comment('Способ оплаты.'), // 4
            'payment_agent_info_id' => $this->integer(11)->null()->comment('Данные платежного агента'),
            'create_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->timestamp()->null(),
        ]);

        $this->createTable('ofd_receipt', [
            'id' => $this->primaryKey(),
            'inn' => $this->string(12)->comment('ИНН лица, от имени которого генерируется кассовый документ (чек)'),
            'type' => $this->string(20)->defaultValue('Income')->comment('Тип формируемого документа'),
            'invoice' => $this->string(20)->comment('Идентификатор счета, на основании которого генерируется чек'),
            'customer_id' => $this->integer(11)->comment('Содержимое клиентского чека'),
            'create_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
            'update_at' => $this->timestamp()->null(),
        ]);

        $this->addForeignKey('fk_ofd_receipt_item_customer', '{{%ofd_receipt_item}}', 'customer_id', '{{%ofd_customer}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_ofd_receipt_item_receipt', '{{%ofd_receipt_item}}', 'receipt_id', '{{%ofd_receipt}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_ofd_customer_agent_info', '{{%ofd_customer}}', 'payment_agent_info_id', '{{%ofd_payment_agent_info}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_ofd_receipt_item_agent_info', '{{%ofd_receipt_item}}', 'payment_agent_info_id', '{{%ofd_payment_agent_info}}', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_ofd_receipt_customer', '{{%ofd_receipt}}', 'customer_id', '{{%ofd_customer}}', 'id', 'CASCADE', 'NO ACTION');

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
