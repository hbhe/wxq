<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_new_attendance".
 *
 * @property int $id
 * @property string $mobile 手机号码
 * @property string $work_date 工作日期
 * @property int $attendance 考勤状态
 * @property int $am_pm 上午或下午
 * @property int $num 迟到早退次数
 * @property int $start_at 考勤开始时间
 * @property int $end_at 考勤结束时间
 */
class NewAttendance extends \yii\db\ActiveRecord
{
    public $name;
    public $department;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_new_attendance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'work_date'], 'required'],
            [['work_date'], 'safe'],
            [['attendance', 'am_pm', 'num', 'start_at', 'end_at'], 'integer'],
            [['mobile'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机号码',
            'work_date' => '工作日期',
            'attendance' => '考勤状态',
            'am_pm' => '时间',
            'num' => '迟到早退次数',
            'start_at' => '考勤开始时间',
            'end_at' => '考勤结束时间',
            'state'=>'长时间在单位',
            'name'=>'姓名',
            'department'=>'部门名称',
        ];
    }
    public static function dayOrhalf(){
        return [0=>'全天',1=>'上午',2=>'下午'];
    }
    public static function attendance(){
        return [0=>'迟到早退',1=>'全勤',2=>'旷工',3=>'事假',4=>'病假',5=>'公休',6=>'出差',7=>'外出',8=>'其它'];
    }
    /**
     * 与attendance建立一对一的关系
     */
    public function getUser(){
        return $this->hasOne(user::className(),['mobile'=>'mobile']);
    }
}
