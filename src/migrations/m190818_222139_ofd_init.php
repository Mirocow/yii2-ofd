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
        $this->createTable('ofd_receipt_status', [
            'id' => $this->primaryKey(),
            'invoice' => $this->string(10),
            'type' => $this->string(15),
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
