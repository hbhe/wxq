<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_attendance".
 *
 * @property int $id
 * @property string $mobile 手机号码
 * @property string $create_at 创建日期
 * @property int $attendance 是否全勤
 * @property tinyint $kuanggong  旷工，一天分两次
 * @property string $remarks 备注
 */
class attendance extends \yii\db\ActiveRecord
{
    public $name;
    public $department;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_attendance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile'], 'required'],
            [['create_at','name','department'], 'safe'],
            [['attendance','kuanggong'], 'integer'],
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
            'create_at' => '创建日期',
            'attendance' => '是否全勤',
            'remarks' => '迟到早退（次数）',
            'name'=>'员工姓名',
            'department'=>'部门名称',
            'kuanggong'=>'旷工次数'
        ];
    }
    /**
     * 获取考勤表中attendance字段值代表的中文意思
     */
    public static function getAttendance(){
        $attendance=array(0=>'迟到早退',1=>'上班',2=>'异常情况',3=>'请假',4=>'休假');
        return $attendance;
    }
    /**
     * 与attendance建立一对一的关系
     */
    public function getUser(){
        return $this->hasOne(user::className(),['mobile'=>'mobile']);
    }
}
