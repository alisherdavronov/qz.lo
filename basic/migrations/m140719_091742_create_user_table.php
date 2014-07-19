<?php

use yii\db\Schema;

class m140719_091742_create_user_table extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => 'pk',
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'name' => Schema::TYPE_STRING,
            'token' => Schema::TYPE_STRING,
        ]);
        $this->createIndex('email', 'user', 'email', true);
        $this->createIndex('token', 'user', 'token', true);
    }

    public function down()
    {
        $this->dropIndex('token', 'user');
        $this->dropIndex('email', 'user');
        $this->dropTable('user');
    }
}
