<?php

use yii\db\Migration;

/**
 * Class m241219_185809_create_bid
 */
class m241219_185809_create_requests extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('requests', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'status' => "ENUM('Active', 'Resolved') NOT NULL DEFAULT 'Active'",
            'message' => $this->text()->notNull(),
            'comment' => $this->text()->null(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->null(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('requests');
    }


}
