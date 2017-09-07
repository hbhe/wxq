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

        $this->insert('{{%suite}}', [
            'title'=>'meeting',
            'sid' => 'cys_meeting',
            'corp_id' => yii::$app->params['corp_id'],
            'suite_id' => 'tj8c2445c93840db09',
            'suite_secret' => '7lZbxTFWPeoMuLrxuXuA-9bGsOuKmS_6_A87VrKaEFc2divG7Uu8dh6O9BZey67T',
            'token' => 'FGPJTQK3vfXxNJh',
            'encodingAESKey' => '4ap9WmYrVRmwo4GKQsVQFlCqH8Bz0YdolmImWWvaOi5',
        ]);

        $this->insert('{{%suite}}', [
            'title'=>'Culture',
            'sid' => 'cys_culture',
            'corp_id' => yii::$app->params['corp_id'],
            'suite_id' => 'tj4a1744a4a878638f',
            'suite_secret' => '3id-N4NUU_Gs6yGLz-b09J51hqUS25X0JXkgbYt37QOJ31Oz7wL6VAKhr4vbMX-U',
            'token' => 'JnSfFvpT',
            'encodingAESKey' => 'yCI1zBU7mt717hsAhjTCLAHyBFWTSxrut6diEJrGDBl',
        ]);

        $this->createTable('{{%agent}}', [
            'id' => $this->primaryKey(),        
            'sid' => $this->string(64)->notNull()->defaultValue('')->unique(),             
            'suite_id' => $this->string(128)->notNull()->defaultValue('')->comment('所属套件'),
            'title' => $this->string(64)->comment('应用名称'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);        

        $this->insert('{{%agent}}', [
            'suite_id' => 'tj8c2445c93840db09',        
            'sid' => 'cys_meeting_agent_meeting',
            'title'=>'cys_meeting_agent_meeting',            
        ]);

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
            'agent_sid' => $this->string(64)->notNull()->defaultValue(''),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);        

/*
        $this->createTable('{{%gh}}', [
            'id' => $this->primaryKey(),        
            'title' => $this->string(32),        
            'client_id' => $this->integer(),            
            'gh_id' => $this->string(32)->notNull()->defaultValue(''),
            'sid' => $this->string(32)->notNull()->defaultValue(''),            
            'appId' => $this->string(64)->notNull()->defaultValue(''),
            'appSecret' => $this->string(64)->notNull()->defaultValue(''),
            'token' => $this->string(32)->notNull()->defaultValue(''),
            'accessToken' => $this->string(512)->defaultValue(''),
            'accessToken_expiresIn' => $this->integer(),
            'encodingAESKey' => $this->string(43),
            'encodingMode' => $this->smallInteger(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
            'jsapiTicket' => $this->string(512),     
            'jsapiTicket_expiresIn' => $this->integer(),            
            'wxPayMchId' => $this->string(32),     
            'wxPayApiKey' => $this->string(64),                 
            'wxcardapiTicket' => $this->string(512),                 
            'wxcardapiTicket_expiresIn' => $this->integer(),
            'sms_template' => $this->string(12),
        ], $tableOptions);        

        $this->insert('{{%gh}}', [
            'title'=>'title',
            'gh_id'=>'7',
            'sid' => 'xianan',
            'appId' => 'wx0b4f26d460868a25',
            'appSecret' => 'xLIilfypLglUMTyvJpfbudXMH1NDfsAY8AM6UiPnq4v_IoKVHyWV34l_exuOFWRX',
            'token' => 'CrnhiziKIWtk74M1lJaDA',
            'encodingAESKey' => 'ddKwpkk2MBgMiTyygYmfBiNz8b44af2srW57azS0czq',
        ]);
*/

/*
        $this->createTable('wxe_agent', [
            'id' => $this->primaryKey(),        
            'title' => $this->string(32),        
            'client_id' => $this->integer(),            
            'agent_id' => $this->integer()->notNull()->defaultValue(0),
            'appId' => $this->string(64)->notNull()->defaultValue('')->comment('corpid'),
            'appSecret' => $this->string(64)->notNull()->defaultValue('')->comment('secret'),
            'token' => $this->string(32)->notNull()->defaultValue(''),
            'encodingAESKey' => $this->string(43)->comment(''),
            'encodingMode' => $this->smallInteger(),
            'accessToken' => $this->string(512)->defaultValue(''),
            'accessToken_expiresIn' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
            'jsapiTicket' => $this->string(512),     
            'jsapiTicket_expiresIn' => $this->integer(),            
            'wxPayMchId' => $this->string(32),     
            'wxPayApiKey' => $this->string(64),                 
            'wxcardapiTicket' => $this->string(512),                 
            'wxcardapiTicket_expiresIn' => $this->integer(),
            'sms_template' => $this->string(12),
        ], $tableOptions);        
*/

/*
        $this->createTable('wx_user', [
            'id' => $this->primaryKey(),        
            'gh_id' => $this->string(32)->notNull()->defaultValue(''),
            'openid' => $this->string(64)->notNull()->defaultValue(''),
            'unionid' => $this->string(64),
            'subscribe' => $this->smallInteger(),
            'subscribe_time' => $this->integer()->unsigned(),
            'nickname' => $this->string(),
            'sex' => $this->smallInteger(),
            'city' => $this->string(32),
            'country' => $this->string(32),
            'province' => $this->string(32),
            'headimgurl' => $this->string(),
            'groupid' => $this->integer(),
            'remark' => $this->string(),
            'mobile' => $this->string(11)->comment('手机号'),      
            'points' => $this->integer()->defaultValue(0),            
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
        ], $tableOptions);
        $this->createIndex('idx_gh_id', 'wx_user', ['gh_id']);        
        $this->createIndex('idx_openid', 'wx_user', ['openid'], true);        
        $this->createIndex('idx_mobile', 'wx_user', ['mobile'], true);        
        
        $this->createTable('wx_client', [
            'id' => $this->primaryKey(),
            'codename' => $this->string(16)->notNull()->unique(),
            'fullname' => $this->string()->notNull()->defaultValue(''),
            'shortname' => $this->string()->notNull()->defaultValue(''),
            'city' => $this->string(32)->defaultValue(''),
            'province' => $this->string(32)->defaultValue(''),
            'country' => $this->string(32)->defaultValue(''),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);
        
        $this->createTable('wx_gh', [
            'id' => $this->primaryKey(),        
            'title' => $this->string(32),        
            'client_id' => $this->integer(),            
            'gh_id' => $this->string(32)->notNull()->defaultValue(''),
            'appId' => $this->string(64)->notNull()->defaultValue(''),
            'appSecret' => $this->string(64)->notNull()->defaultValue(''),
            'token' => $this->string(32)->notNull()->defaultValue(''),
            'accessToken' => $this->string(512)->defaultValue(''),
            'accessToken_expiresIn' => $this->integer(),
            'encodingAESKey' => $this->string(43),
            'encodingMode' => $this->smallInteger(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),            
            'jsapiTicket' => $this->string(512),     
            'jsapiTicket_expiresIn' => $this->integer(),            
            'wxPayMchId' => $this->string(32),     
            'wxPayApiKey' => $this->string(64),                 
            'wxcardapiTicket' => $this->string(512),                 
            'wxcardapiTicket_expiresIn' => $this->integer(),
            'sms_template' => $this->string(12),
        ], $tableOptions);        
        $this->createIndex('idx_gh_id', 'wx_gh', ['gh_id'], true);        

        $this->createTable('wx_msg_log', [
            'id' => $this->primaryKey(),
            'ToUserName' => $this->string(64)->notNull(),
            'FromUserName' => $this->string(64)->notNull(),
            'CreateTime' => $this->integer(),
            'MsgType' => $this->string(),
            'WholeMsg' => $this->string(1024),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
            'elapsetime' => $this->float()            
        ], $tableOptions);

        $this->createTable('wx_menu', [
            'id' => $this->primaryKey(),
            'gh_id' => $this->string(64)->notNull(),
            'name' => $this->string(40)->notNull(),
            'parent_id' => $this->integer(),
            'type' => $this->string(32),
            'key' => $this->string(512),
            'order' => $this->smallInteger(),
            'sub_button_flag' => $this->smallInteger(),
        ], $tableOptions);

        $this->createTable('wx_point_log', [
            'id' => $this->primaryKey(),
            'openid' => $this->string(32)->notNull(),
            'amount' => $this->integer(),
            'category' => $this->string(32),
            'comment' => $this->string(64),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('wx_outlet', [
            'id' => $this->primaryKey(),
            'sid' => $this->string(32),            
            'client_id' => $this->integer()->notNull(),
            'business_name' => $this->string(),
            'branch_name' => $this->string(),
            'categories' => $this->string(),
            'province' => $this->string(16),
            'city' => $this->string(16),
            'district' => $this->string(16),
            'address' => $this->string(),
            'longitude' => $this->double(),
            'latitude' => $this->double(),
            'offset_type' => $this->smallInteger()->defaultValue(1),
            'telephone' => $this->string(),
            'introduction' => $this->text(),
            'recommend' => $this->string(),
            'special' => $this->string(),
            'open_time' => $this->string(),
            'avg_price' => $this->integer(),
            'self_operated' => $this->smallInteger()->notNull()->defaultValue(0),
            'online' => $this->smallInteger()->notNull()->defaultValue(1),            
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('wx_outlet_employee', [
            'id' => $this->primaryKey(),      
            'outlet_id' => $this->integer()->notNull(),            
            'employee_name' => $this->string(),
            'employee_mobile' => $this->string(11)->notNull(),
            'employee_role' => $this->string(32)->notNull()->defaultValue('employee'),
        ], $tableOptions);
        $this->createIndex('idx_outlet_id_mobile', 'wx_outlet_employee', ['outlet_id', 'employee_mobile'], true);      

        $this->createTable('wx_gh_outlet', [
            'id' => $this->primaryKey(),              
            'gh_id' => $this->string(32)->notNull(),
            'outlet_id' => $this->integer()->notNull(),
            'poi_id' => $this->string(32),
            'available_state' => $this->integer(),
            'update_status' => $this->integer(),
            'photo_list' => $this->string(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);    
        $this->createIndex('idx_gh_id_outet_id', 'wx_gh_outlet', ['gh_id', 'outlet_id'], true);      

        $this->createTable('wx_template_id', [
            'gh_id' => $this->string(64)->notNull(),
            'template_id_short' => $this->string(32)->notNull(),
            'template_id' => $this->string(128)->notNull(),
        ], $tableOptions);
*/
    }

    public function down()
    {

        $this->dropTable('{{%corp}}');

        $this->dropTable('{{%suite}}');

        $this->dropTable('{{%agent}}');

        $this->dropTable('{{%corp_suite}}');

        $this->dropTable('{{%corp_agent}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

/*
        $this->insert('{{%corp}}', [
            'corp_id' => 'wx0b4f26d460868a25',
            'username' => 'cys',
            'password_hash'=>Yii::$app->getSecurity()->generatePasswordHash('cys'),
            'auth_key'=>Yii::$app->getSecurity()->generateRandomString(),
        ]);

        $this->insert('{{%corp}}', [
            'corp_id' => 'wxe675e8d30802ff44',
            'username' => 'hope',
            'password_hash'=>Yii::$app->getSecurity()->generatePasswordHash('hope'),
            'auth_key'=>Yii::$app->getSecurity()->generateRandomString(),
        ]);
*/
