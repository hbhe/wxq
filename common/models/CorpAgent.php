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
            [['corp_id', 'agent_sid'], 'string', 'max' => 128],
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
            'agentid' => 'AgentID',        // 使用方应用实例ID, 每安装一个应用, 企业号就为它分配一个id, 从1开始
            'agent_id' => 'Agent ID',       // 表明此应用属于哪个Agent应用, 不同的企业安装同一个应用时, agent_id相等,而agentid不等
            'agent_sid' => 'Agent SID',       // 字符串ID, 尽量用这个吧
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
        //return $this->hasOne(Agent::className(), ['id' => 'agent_id']);
        return $this->hasOne(Agent::className(), ['sid' => 'agent_sid']);
    }    
    
}
