<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%suite}}".
 *
 * @property int $id
 * @property string $sid
 * @property string $title
 * @property string $corp_id
 * @property string $suite_id
 * @property string $suite_secret
 * @property string $suite_ticket
 * @property string $token
 * @property string $auth_code
 * @property string $permanent_code
 * @property string $accessToken
 * @property int $accessToken_expiresIn
 * @property string $encodingAESKey
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
//class Suite extends \common\wosotech\base\ActiveRecord
class Suite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%suite}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['sid'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 64],
            [['corp_id', 'suite_id', 'token'], 'string', 'max' => 128],
            [['suite_secret', 'suite_ticket'], 'string', 'max' => 256],
            [['encodingAESKey'], 'string', 'max' => 43],
            [['sid'], 'unique'],
            [['status'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sid' => '编码',
            'title' => '标题',
            'corp_id' => '企业ID',
            'suite_id' => '套件ID',
            'suite_secret' => '套件Secret',
            'suite_ticket' => '套件Ticket',
            'token' => 'Token',
            'encodingAESKey' => '加解密密钥',
            'status' => '状态 ',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'corp_id',
                ],
                'value' => function ($event) {
                    return yii::$app->params['corp_id'];
                },
            ],

            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'value' => new \yii\db\Expression('NOW()'),
            ],
            
        ];
    }
        
    /**
     * @inheritdoc
     * @return SuiteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SuiteQuery(get_called_class());
    }

    public function getAgents()
    {
        return $this->hasMany(Agent::className(), ['suite_id' => 'suite_id']);
    }

    public function getCorp()
    {
        return $this->hasOne(Corp::className(), ['corp_id' => 'corp_id']);
    }

    /**
     * @return null|object|\wechat\models\QyWechat
     */
    public function getQyWechat()
    {
        if (yii::$app->has('QyWechat')) {
            return yii::$app->get('QyWechat');
        }
        
        $options = [
            'token' => $this->token,
            'encodingaeskey' => $this->encodingAESKey,
            'appid' => isset($_GET["echostr"]) ? $this->corp_id : $this->suite_id,  // suitid
            'appsecret' => $this->suite_secret, // suit secret
            'agentid'=> 0,
            'debug' => true,
            'logcallback'=>'yii::error',
        ];

        $we = new \wechat\models\QyWechat($options);
        $we->setSuiteTicket($this->suite_ticket);    
        Yii::$app->set('QyWechat', $we);        
        
        return  $we;              
        
    }

}
