<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%corp_agent}}".
 *
 * @property int $id
 * @property string $corp_id
 * @property string $agent_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class CorpAgent extends \common\wosotech\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%corp_agent}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'agent_id', 'agentid'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['corp_id'], 'string', 'max' => 128],
            [['status', 'agent_id', 'agentid'], 'default', 'value' => 0],
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
            'agentid' => 'AgentID',        // ʹ�÷�Ӧ��ʵ��ID, ÿ��װһ��Ӧ��, ��ҵ�ž�Ϊ������һ��id, ��1��ʼ
            'agent_id' => 'Agent ID',       // ������Ӧ�������ĸ�AgentӦ��, ��ͬ����ҵ��װͬһ��Ӧ��ʱ, agent_id���,��agentid����
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return CorpAgentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CorpAgentQuery(get_called_class());
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

    public function getCorp()
    {
        return $this->hasOne(Corp::className(), ['corp_id' => 'corp_id']);
    }    

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), ['id' => 'agent_id']);
    }    
    
}
