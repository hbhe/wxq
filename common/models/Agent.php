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
            'sid' => 'Sid',
            'suite_id' => 'Suite ID',
            'title' => 'Title',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
