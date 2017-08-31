<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_meeting".
 *
 * @property int $id
 * @property string $author 会议发起人
 * @property string $title 会议名称
 * @property string $meeting_time 会议开始时间
 * @property string $addr 会议地址
 * @property string $create_at 添加时间
 */
class Meeting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_meeting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author', 'title'], 'required'],
            [['meeting_time', 'create_at'], 'safe'],
            [['author', 'title'], 'string', 'max' => 64],
            [['addr','content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author' => '会议发起人',
            'title' => '会议名称',
            'meeting_time' => '会议开始时间',
            'addr' => '会议地址',
            'create_at' => '添加时间',
            'content'=>'注意事项',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($insert) {
                $this->create_at =date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }
}
