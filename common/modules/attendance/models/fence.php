<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_fence".
 *
 * @property int $id
 * @property int $attendance_id 考勤id
 * @property string $create_at 创建时间
 * @property int $pass 进出电子围栏
 */
class fence extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_fence';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attendance_id', 'create_at'], 'required'],
            [['attendance_id', 'pass'], 'integer'],
            [['create_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attendance_id' => '考勤人id',
            'create_at' => '发生时间',
            'pass' => '进出电子围栏',
        ];
    }
    /**
     * 获取进出电子围栏的状态数组
     * @return [array] pass值代表的汉字数组
     */
    public function getPass($key=0){
        $pass=array(0=>'班中出',1=>'班中进',2=>'按时下班',3=>'早退',4=>'迟到',5=>'按时上班');
        return $pass[$key];
    }
}
