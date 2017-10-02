<?php

use yii\db\Migration;

class m161116_115156_create_init_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 ';
        }

        $this->createTable('{{%corp}}', [
            'id' => $this->primaryKey(),        
            'corp_id' => $this->string(128)->notNull()->defaultValue('')->unique(),
            'corp_name' => $this->string(64)->comment('使用套件的客户'),
            'corp_type' => $this->string(64),            
            'corp_round_logo_url' => $this->string(512),            
            'corp_square_logo_url' => $this->string(512),            
            'corp_user_max' => $this->integer()->notNull()->defaultValue(0),
            'corp_agent_max' => $this->integer()->notNull()->defaultValue(0),
            'corp_wxqrcode' => $this->string(512),            
            'corp_full_name' => $this->string(512),            
            'subject_type' => $this->integer()->notNull()->defaultValue(0),
            'userid' => $this->string(32),
            'mobile' => $this->string(32),
            'username' => $this->string()->unique(),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string(64)->unique(),
            'access_token' => $this->string(64),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);        

        $this->createTable('{{%suite}}', [
            'id' => $this->primaryKey(),        
            'sid' => $this->string(32)->notNull()->defaultValue('')->unique(),
            'title' => $this->string(64),
            'corp_id' => $this->string(128)->notNull()->defaultValue(''), //
            'suite_id' => $this->string(128)->notNull()->defaultValue(''),
            'suite_secret' => $this->string(256)->notNull()->defaultValue(''),
            'suite_ticket' => $this->string(256)->notNull()->defaultValue(''),            
            'token' => $this->string(128)->notNull()->defaultValue(''),
//            'auth_code' => $this->string(512)->notNull()->defaultValue(''),      
//            'permanent_code' => $this->string(512)->defaultValue(''),                        
//            'accessToken' => $this->string(512)->defaultValue(''),
//            'accessToken_expiresIn' => $this->integer(),
            'encodingAESKey' => $this->string(43),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);   

        $this->createTable('{{%agent}}', [
            'id' => $this->primaryKey(),        
            'sid' => $this->string(64)->notNull()->defaultValue('')->unique(),             
            'suite_id' => $this->string(128)->notNull()->defaultValue('')->comment('所属套件'),
            'title' => $this->string(64)->comment('应用名称'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);        

        $this->createTable('{{%corp_suite}}', [
            'id' => $this->primaryKey(),        
            'corp_id' => $this->string(128)->notNull()->defaultValue(''),
            'suite_id' => $this->string(128)->notNull()->defaultValue(''),
            'permanent_code' => $this->string(512)->defaultValue(''),                        
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);        

        $this->createTable('{{%corp_agent}}', [
            'id' => $this->primaryKey(),        
            'corp_id' => $this->string(128)->notNull()->defaultValue(''),
            'agentid' =>  $this->integer()->notNull()->defaultValue(0),
            'agent_id' =>  $this->integer()->notNull()->defaultValue(0),
            'agent_sid' => $this->string(128),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);
    }

    public function down()
    {

        $this->dropTable('{{%corp}}');

        $this->dropTable('{{%suite}}');

        $this->dropTable('{{%agent}}');

        $this->dropTable('{{%corp_suite}}');

        $this->dropTable('{{%corp_agent}}');
    }

}

