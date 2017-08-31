<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_participant".
 *
 * @property int $id
 * @property int $meeting_id 会议id
 * @property string $userid 参会人员userid
 * @property int $status 参会状态 1=确认 2=请假 3=签到
 * @property string $status_time 状态改变时间
 */
class Participant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_participant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'userid'], 'required'],
            [['meeting_id', 'status'], 'integer'],
            [['status_time'], 'safe'],
            [['userid'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'meeting_id' => '会议id',
            'userid' => '参会人员userid',
            'status' => '参会状态 1=确认 2=请假 3=签到',
            'status_time' => '状态改变时间',
        ];
    }
}
