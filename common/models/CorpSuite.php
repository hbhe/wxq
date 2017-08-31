<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%corp_suite}}".
 *
 * @property int $id
 * @property string $corp_id
 * @property string $suite_id
 * @property string $permanent_code
 * @property string $accessToken
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class CorpSuite extends \common\wosotech\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%corp_suite}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['corp_id', 'suite_id'], 'string', 'max' => 128],
            [['permanent_code'], 'string', 'max' => 512],
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
            'corp_id' => 'Corp ID',
            'suite_id' => 'Suite ID',
            'permanent_code' => 'Permanent Code',
            'accessToken' => 'Access Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return CorpSuiteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CorpSuiteQuery(get_called_class());
    }

    public function getCorp()
    {
        return $this->hasOne(Corp::className(), ['corp_id' => 'corp_id']);
    }    

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'value' => new \yii\db\Expression('NOW()'),
            ],            
        ];
    }

    public function getSuite()
    {
        return $this->hasOne(Suite::className(), ['suite_id' => 'suite_id']);
    }    

    public function getSuiteAccessToken()
    {
        $we = $this->suite->getQyWechat();
        $token = $we->getSuiteAccessToken($this->corp_id, $this->permanent_code);
        
        return $token;
    }    

    public function afterDelete()
    {
        $agents = $this->suite->agents;
        foreach($agents as $agent) {
            if (null !== ($model = CorpAgent::findOne(['corp_id' => $this->corp_id, 'agent_id' => $agent->id]))) {
                $model->delete();
            }
        }
        $this->trigger(self::EVENT_AFTER_DELETE);
    }

    public function getQyWechat()
    {
        $we = $this->suite->getQyWechat();
        $token = $we->getSuiteAccessToken($this->corp_id, $this->permanent_code);  
        $we->checkAuth('', '', $token);   
        
        return $we;
    }    
}
