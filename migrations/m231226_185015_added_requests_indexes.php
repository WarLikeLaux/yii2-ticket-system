<?php

use yii\db\Migration;

/**
 * Class m231226_185015_added_requests_indexes
 */
class m231226_185015_added_requests_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-requests-status',
            '{{%requests}}',
            'status'
        );

        $this->createIndex(
            'idx-requests-created_at',
            '{{%requests}}',
            'created_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-requests-status',
            '{{%requests}}'
        );

        $this->dropIndex(
            'idx-requests-created_at',
            '{{%requests}}'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231226_185015_added_requests_indexes cannot be reverted.\n";

        return false;
    }
    */
}
