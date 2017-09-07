<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%agent}}".
 *
 * @property int $id
 * @property string $sid
 * @property string $suite_id
 * @property string $title
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Agent extends \common\wosotech\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%agent}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['sid', 'title'], 'string', 'max' => 64],
            [['suite_id'], 'string', 'max' => 128],
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
            'suite_id' => '套件ID',
            'title' => '标题',
            'status' => '状态 ',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @inheritdoc
     * @return AgentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AgentQuery(get_called_class());
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
}
