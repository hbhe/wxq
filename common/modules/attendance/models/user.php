<?php

namespace common\modules\attendance\models;
use common\modules\attendance\models\NewAttendance;
use Yii;

/**
 * This is the model class for table "wxe_user".
 *
 * @property int $id
 * @property string $userid 成员userid
 * @property string $name 成员名称
 * @property string $department 成员所属部门
 * @property string $position 职位信息
 * @property string $mobile 手机号码
 * @property int $gender 性别
 * @property string $email 电子邮箱
 * @property string $weixinid 微信号
 * @property string $avatar 头像url
 * @property string $status 是否关注
 * @property string $extattr 扩展属性
 * @property int $leader 是否领导
 * @property string $state 是否在单位
 * @property string $update_at 更新时间
 * @property double $lng 经度
 * @property double $lat 纬度
 * @property [string] $corpid [企业id]
 */
class user extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_department_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['leader'], 'required'],
            [['gender', 'leader','status','admin'], 'integer'],
            [['update_at','leader'], 'safe'],
            [['lng', 'lat','state'], 'number'],
            [['userid', 'email', 'weixinid'], 'string', 'max' => 64],
            [['name', 'position'], 'string', 'max' => 50],
            [['department', 'avatar', 'extattr'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 11],
            //[['corpid'], 'string', 'max' => 18],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => '成员userid',
            'name' => '成员名称',
            'department' => '成员所属部门',
            'position' => '职位信息',
            'mobile' => '手机号码',
            'gender' => '性别',
            'email' => '电子邮箱',
            'weixinid' => '微信号',
            'avatar' => '头像url',
            'status' => '关注状态',
            'extattr' => '扩展属性',
            'leader' => '领导权限',
            'state'=>'是否在单位',
            'update_at' => '更新时间',
            'lng' => '经度',
            'lat' => '纬度',
            'admin'=>'管理员',
            'corpid'=>'企业id',
        ];
    }
    /**
     * @获取关注状态
     * @return [array] [状态数组]
     */
    public static function  getStatus()
    {
        $status=array(1=>'已关注',2=>'已禁用',4=>'未关注');
        return $status;
    }
    /**
     * 与attendance表建立一对多关联
     */
    public function getAttendances(){
        $corpid=Yii::$app->session->get('corpid');
        return $this->hasMany(attendance::className(),['mobile'=>'mobile'])->onCondition(['wxe_department_user.corpid'=>$corpid]);
    }
    /**
     * 与NewAttendance表建立一对多关联
     */
    public function getNewAttendances(){
        return $this->hasMany(NewAttendance::className(),['mobile'=>'mobile']);
    }
    /**
     * user.departmen与departmen.name对应起来
     * @param int $id 部门id字符串
     * @param array $department [description]
     * @return department.name
     */
    public static function idToName($id,$department){
        if(strpos($id, ';')){
           // 对应多个部门
            $arr=explode(';', $id);
            // $str='';
            // foreach ($arr as $v) {
                //取出第一个单位，除去去树形结构中的空格
                $str=ltrim($department[$arr[0]],'　');
            // }
            return $str;
        }else{
            //只对应一个部门，返回去除空格的部门名称
            if($id=='')return false;
            return ltrim($department[$id],'　');
        }
    }
    public static function getUsers($corpid){
         $department=(new \yii\db\Query())
            ->select(['name','department','path'])
            ->from('wxe_department')
            ->where(['corpid'=>'$corpid'])
            ->orderBy('path')
            ->all();
    }
}
