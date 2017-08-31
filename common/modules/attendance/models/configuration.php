<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_configuration".
 *
 * @property int $id
 * @property int $department_id 单位id
 * @property string $name 配置名称
 * @property string $value 配置值
 */
class configuration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_department_configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id', 'name'], 'required'],
            [['department_id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['value'], 'string', 'max' => 255],
            [['corpid'], 'string', 'max' => 18],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_id' => '部门名称',
            'name' => '配置名称',
            'value' => '配置值',
            'corpid'=>'企业id',
        ];
    }
    /**
     * @获取配置列表
     * @return [array] [状态数组]
     */
    public static function  getConfig()
    {
        $config=array(
            'baseUrl'=>'服务器地址',
            'corpid'=>'企业号id',
            'time'=>'配置信息时间',
            'toMobile'=>'toMobile',
            'interval_time'=>'手机经纬度未改变的报警时长',
            'fence_lng'=>'电子围栏经度',
            'fence_lat'=>'电子围栏纬度',
            'password'=>'查看巡查密码',
            'fence_radius'=>'电子围栏半径',
            'attendance_start_time'=>'考勤开始时间',
            'attendance_end_time'=>'考勤结束时间',
            'attendance_rest_start_time'=>'午休开始时间',
            'attendance_rest_end_time'=>'午休结束时间',
            'downLoadUrl'=>'app下载地址',
            'versionCode'=>'app版本号',
            'attendanceOffset'=>'考勤偏移值（分钟）',
            'attendanceInterval'=>'位置信息提交间隔（秒）',
            'pmWorkTimeOffset'=>'下午允许提前打卡时间（分）',
        );
        return $config;
    }
    /**
     * 根据corpid查询配置信息最后更改的时间戳
     * @return [string] [时间戳]
     */
    public static function getTime($corpid){
        $time=(new \yii\db\Query())
                ->select(['value'])
                ->from('wxe_department_configuration')
                ->where(['corpid'=>$corpid,'name'=>'time'])
                ->scalar();
        return $time;
    }
    /**
     * 根据手机号，查询对应的部门id
     * @param  [string] $mobile [手机号码]
     * @return [string]         部门id
     */
    public static function getDepartmentId($mobile){
        $id=(new \yii\db\Query())
                ->select(['department'])
                ->from('wxe_department_user')
                ->where(['mobile'=>$mobile])
                ->scalar();
        $id=str_replace(';', ',', $id);
        return $id;
    }
    /**
     * 根据部门id和企业id查询配置数组
     * 查询的一维数组，以name为键名，子部门的重复的键值对会覆盖父级键值对
     * @param  [type] $department_id [description]
     * @param  [type] $corpid        [description]
     * @return [type]                [description]
     */
    public static function getConfiguration($department_id,$corpid=''){
        $id_arr=explode(',',$department_id);
        $configuration=(new \yii\db\Query())
                ->select(['value'])
                ->from('wxe_department_configuration')
                ->where(['or',['in','department_id',$id_arr],['department_id'=>1]])
                ->orderBy(['department_id'=>'asc'])
                ->indexBy('name')
                ->column();
        return $configuration;
    }
    /**
     * 查询是否是工作日
     * @param  [type]  $corpid 企业号id
     * @return ‘0’代表休息日，‘1’代表工作日
     */
    public static function isWorkDay($corpid){
        $today=date('Y-m-d');
        $workday=(new \yii\db\Query())
                ->select(['is_work_day'])
                ->from('wxe_workday')
                ->where(['and',['corpid'=>$corpid],['date'=>$today]])
                ->scalar();
        //如果没有查询到，就按周末为休息日返回数据
        if($workday==null){
             $w=date('w',strtotime($today));
             if($w==0 or $w==6){
                return '0';
             }else{
                return '1';
             }
        }
        return $workday;
    }
}
