<?php

use yii\db\Migration;

/**
 * Class m200708_165255_alter_table_status
 */
class m200708_165255_alter_table_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('ofd_receipt_status', 'receiptId', $this->string(36)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200708_165255_alter_table_status cannot be reverted.\n";

        return false;
    }
}
