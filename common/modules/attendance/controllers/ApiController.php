<?php
namespace common\modules\attendance\controllers;
use common\modules\attendance\models\configuration;
use common\modules\attendance\models\fence;
use common\modules\attendance\models\user;
use common\modules\attendance\models\attendance;
use common\modules\attendance\models\department;
use common\modules\attendance\models\workday;
use Yii;
/**
 * 本控制器因为使用频繁，所以尽量写在一个页面里，减少文件的调用，加快执行速度
 * 接口1 config 返回请求的配置信息
 * 接口2 fence 处理提交进出电子围栏的状态信息
 * 接口3 position 接受10分钟一次的传输位置信息
 * 接口4 status 返回员工不在单位或者异常的信息
 * 接口5 workday 返回工作的日历时间
 * 接口6 Attendance 返回指定人员的本月考勤信息
 */
class ApiController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    /**
     * 返回配置信息
     * @param  [type] $mobile [手机号码]
     * @return [type] json [配置信息]
     */
    // public function actionConfig($flag)
    // {
    //     header("Content-type: text/html; charset=utf-8"); 
    //     //获取企业id（corpid）
    //     $corpid=Yii::$app->request->get('corpid','ww1de94f786c71be9f');
    //     //获取手机号码和时间戳数组
    //     $flag=$this->decode($flag);
    //     //初始化配置数组
    // 	$configuration['msg']='err';
    //     $configuration['isWorkDay']=configuration::isWorkDay($corpid);//查询当天是否是工作日
    //     $configuration['nowtime']=time();

    // /****如果传递的数据符合条件，则进行配置参数查询*****/
    //     if($flag && is_numeric($flag['mobile']) &&strlen($flag['mobile'])==11){
    //         //修改配置数组
    //     	$configuration['msg']='ok';
    //         //根据cordid查询配置信息的time时间戳
    //         $time=configuration::getTime($corpid);
    //         //比对$flag['time']与配置信息更改的时间戳,如果不一致，查询手机号码所对应的部门id。
    //         if($time!=$flag['time']){
    //             $department_id=configuration::getDepartmentId($flag['mobile']);
    //             //再根据部门id和cordid查询配置信息，如果是多部门的，说明是领导（分管多个部门）返回总部信息，否则直接返回职工所在部门的配置信息
    //             if(strstr($department_id,',')==false){
    //                 $configuration['config']=configuration::getConfiguration($department_id,$corpid);
    //             }else{
    //                 $configuration['config']=configuration::getConfiguration(1,$corpid);
    //             }
    //         }
    //     }
    //     //转换为json数组，输出
    //     echo  json_encode($configuration,true);
    //     exit(0);
    // }
    // /**
    //  * 提交进出电子围栏信息,只对应attendance和fence表，对应统计功能，不负责实时位置的功能attendance_end_time
    //  * @param [type] $a [进出电子围栏信息]
    //  * @param [type] $flag [标识信息]
    //  * @return [type] [description]
    //  */

    // public function actionFence(){
    //     header("Content-type: text/html; charset=utf-8"); 
    //     //获得提交数据
    //     $post=Yii::$app->request->post();
    //     if(empty($post)){
    //         echo ('no post data!');
    //         exit(0);
    //     }
    //     //解码处理数据结构，获得需要的数据变量
    //     $flag=$this->decode($post['flag']);
    //     $list=json_decode($post['list'],true);
    //     $mobile=$flag['mobile'];
    //     //初始化返回信息数组
    //     $msg = array('msg' =>'err' , 'time'=>time());
    //     //如果提交的数据有效,则新增fence模型的数据，修改实时状态信息
    //     if($list && is_numeric($mobile) && strlen($mobile)==11){
    //         //user不存在，直接返回错误
    //         $user=user::find()->where(['mobile'=>$mobile])->asArray()->one();
    //         if($user==null){
    //             $msg['msg']='ok';
    //             $msg['status']='The mobile is invalid!';
    //             echo json_encode($msg);
    //             exit(0);
    //         }
    //         foreach ($list as $k => $v) {
    //             // 判断提交的数据时间不是工作日，直接跳出循环，app没做判断，所以只有这里判断
    //             $date=date('Y-m-d',$v['time']);
    //             $workday=workday::find()->where(['date'=>$date,'is_work_day'=>0])->asArray()->one();
    //             if($workday){
    //                 break;
    //             }
    //             $model=attendance::find()->where(['mobile'=>$mobile,'create_at'=>date('Y-m-d',$v['time'])])->one();
    //             if($model && $model->attendance > 2){
    //                 break;
    //             }
    //             // 根据状态值，分别进行考勤管理
    //             if($v['a']==1){//上班
    //                 //插入一条attention数据（考勤信息）1代表是全勤，0代表迟到早退
    //                 $time=$this->getStartTime($user['department'],$user['corpid'],$v['time']);//获取配置信息中的考勤开始时间
    //                 $start_time=date('Y-m-d',$v['time']).' '.$time;//当日考勤开始时间
    //                 $attendance=1;
    //                 if($v['time']>(strtotime($start_time)+1200)){
    //                     $attendance=0;//如果晚点超过20分钟到达电子围栏，则是迟到
    //                 }
    //                 $attendance_id=$this->addAttendance($mobile,$attendance,$user['corpid'],$v['time']);
    //                 //最后插入一条进入电子围栏的信息
    //                 $this->inOut($attendance_id,$v['time'],1);
    //                 //修改实时考勤状态为上班状态
    //                 $this->updateState(1,time(),$mobile);
    //                 $msg['status']='Go to the office!';
    //             }elseif ($v['a']==3) {//早退
    //                 //早退，修改attention表中的全勤信息为不是全勤，并给remarks加1
    //                 $time=$this->getEndTime($user['department'],$user['corpid'],$v['time']);//获取配置信息中的考勤结束时间
    //                 $end_time=date('Y-m-d',$v['time']).' '.$time;//当日考勤结束时间
    //                 $attendance=1;
    //                 $this->updateAttendance($mobile,$v['time']);
    //                $msg['status']='Leave early!';
    //             }elseif ($v['a']==4) {//班中进电子围栏
    //                 //班中进电子围栏,先获得获得考勤id
    //                 $model=attendance::find()->where(['mobile'=>$mobile,'create_at'=>date('Y-m-d',$v['time'])])->one();

    //                 //班中进电子围栏,插入一条进电子围栏信息
    //                 $this->inOut($model->id,$v['time'],'1');
    //                 //修改实时考勤状态为在单位的状态
    //                 $this->updateState(1,time(),$mobile);
    //                 $msg['status']='Come in!';
    //             }elseif($v['a']==5){//班中出电子围栏
    //                 //班中出电子，查找当天当人的考勤id
    //                 $attendance=attendance::find()->where(['mobile'=>$mobile,'create_at'=>date('Y-m-d',$v['time'])])->asArray()->one();
    //                 $id=$attendance['id'];
    //                 //插入一条出电子围栏的信息
    //                 $this->inOut($id,$v['time'],0);
    //                 //修改实时考勤状态为不在单位的状态
    //                 $this->updateState(2,time(),$mobile);
    //                 $msg['status']='Come out!';
    //             }elseif($v['a']==6){//按时下班
    //                 // 查找当天考勤的id
    //                 $model=attendance::find()->select('id')->where(['mobile'=>$mobile,'create_at'=>date('Y-m-d',$v['time'])])->asArray()->one();
    //                 //正常下班，插入一条出电子围栏的信息
    //                 $this->inOut($model['id'],$v['time'],0);
    //                 //修改实时考勤状态为上班状态
    //                 $this->updateState(2,time(),$mobile);
    //                 $msg['status']='Come off duty!';
    //             }elseif($v['a']==7){
    //                 //将手机长时间停放在一个地方的异常信息保存到remark备注里
    //                 $attendance=attendance::find()->where(['mobile'=>$mobile,'create_at'=>date('Y-m-d',$v['time'])])->one();
    //                 if(isset($attendance->remarks)){
    //                     $attendance->remarks='0';
    //                     $attendance->save();
    //                 }
    //             }
    //         }
    //         $msg['msg']='ok';
    //     }
    //     echo json_encode($msg);
    //     exit(0);
    // }
    // /**
    //  * 每过10分钟发一次定位信息,
    //  *@param [type] $p[经纬度]
    //  *@param [type] $flag [标识信息]
    //  * @return [type] [description]
    //  */
    // public function actionPosition(){
    //     header("Content-type: text/html; charset=utf-8"); 
    //     //获得提交参数
    //     $request=Yii::$app->request;
    //     $lng=$request->get('lng',0);
    //     $lat=$request->get('lat',0);
    //     $state=$request->get('state',1);
    //     $flag=$request->get('flag');
    //     //解码
    //     $flag=$this->decode($flag);
    //     $time=$flag['time'];
    //     $mobile=$flag['mobile'];
    //     //初始化返回信息
    //     $msg = array('msg' =>'err' , 'time'=>time());
    //     //如果提交的数据有效
    //     if($flag && is_numeric($mobile) &&strlen($mobile)==11){
    //         //user不存在，直接返回错误
    //         $user=user::find()->select('id')->where(['mobile'=>$mobile])->asArray()->one();
    //         if($user==null){
    //             $msg['status']='The mobile is invalid!';
    //             echo json_encode($msg);
    //             exit(0);
    //         }
    //         Yii::$app->db->createCommand()
    //             ->update('wxe_department_user',['lat'=>$lat,'lng'=>$lng,'state'=>$state,'update_at'=>date('Y-m-d H:i:s',$time)],"id={$user['id']}")
    //             ->execute();
    //         $msg['msg']='ok';
    //         $msg['status']='update success!';
    //     }
    //     echo json_encode($msg);
    //     exit(0);
    // }

    // /**
    //  * 获取在电子围栏之外的或者未考勤的部门员工情况【0=>异常情况，1=>在单位，2=>不在单位,3=休息时间】
    //  *@param [type] $flag [标识信息]
    //  * @return [type] [json]
    //  */
    // public function actionStatus(){
    //     header("Content-type: text/html; charset=utf-8"); 
    //     //获得提交参数
    //     $request=Yii::$app->request;
    //     $flag=$request->get('flag');
    //     $corpid=$request->get('corpid','ww1de94f786c71be9f');//获取企业id（corpid）
    //     //解码
    //     $flag=$this->decode($flag);
    //     $mobile=$flag['mobile'];
    //     $msg = array('msg' =>'err' , 'time'=>time());
    //     //如果提交的数据有效
    //     if($flag && is_numeric($mobile) &&strlen($mobile)==11){
    //         //如果提交的电话号码在数据库中，查找相应数据返回。
    //         $user=user::find()->select(['name','department_path'=>'department','leader','update_at','mobile','state','lng','lat'])
    //             ->where(['mobile'=>$mobile])->asArray()->one();
    //         if($user==null){
    //             $msg['msg']='The mobile is invalid!';
    //             echo json_encode($msg);
    //             die;
    //         }
    //         //根据权限查询实时信息
    //         if($user['leader']==10){
    //             //如果是普通员工
    //             unset($user['leader']);
    //             $users[0]=$user;
    //         }elseif ($user['leader']==5) {
    //             // 如果是部门领导权限
    //             $where=$this->getWhere($user['department_path'],$corpid,$mobile);
    //             $users=user::find()->select(['name','department_path'=>'department','update_at','mobile','state','lng','lat'])->where($where)->orderBy('leader asc')->asArray()->all();
    //         }elseif ($user['leader']==0) {
    //             //如果是最高领导权限
    //             $users=user::find()->select(['name','department_path'=>'department','update_at','mobile','state','lng','lat'])
    //                 ->where(['and',['corpid'=>$corpid],['or',['state'=>2],['mobile'=>$mobile],['<','update_at',date('Y-m-d H:i:s',time()-1200)]]])
    //                 ->orderBy('leader')->asArray()->all();
    //         }
    //         $msg['status']=$this->transform($users,$corpid);
    //         $msg['msg']='ok';
    //     }
    //     echo json_encode($msg,true);
    //     exit(0);
    // }
    // /**
    //  * 获取本周的工作日列表
    //  * @return [type] [description]
    //  */
    // public function actionWorkday(){
    //     header("Content-type: text/html; charset=utf-8"); 
    //     // $corpid=Yii::$app->request->get('corpid','ww1de94f786c71be9f');//获取corpid
    //     $corpid='ww1de94f786c71be9f';
    //     //本周起始日期
    //     $start=date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
    //     $end=date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));
    //     //构造查询
    //     $workday=(new \yii\db\Query())
    //         ->select(['date','is_work_day'])
    //         ->from('wxe_workday')
    //         ->where(['and',['corpid'=>$corpid],['between','date',$start,$end]])
    //         ->orderBy('id')
    //         ->all();
    //     //初始化参数
    //     $msg = array('msg' =>'err' , 'time'=>time());
    //     if($workday){
    //         $msg['msg']='ok';
    //         $msg['workday']=$workday;
    //     }
    //     echo json_encode($msg,true);
    //     exit(0);
    // }
    // /**
    //  * 获取指定人员指定月份的考勤信息
    //  * @return [json] [考勤的字符串]
    //  */
    // public function actionAttendance(){
    //     header("Content-type: text/html; charset=utf-8"); 
    //     //获得提交参数
    //     $request=Yii::$app->request;
    //     $flag=$request->get('flag');
    //     $corpid=$request->get('corpid','ww1de94f786c71be9f');//获取企业id（corpid）
    //     //解码
    //     $flag=$this->decode($flag);
    //     $mobile=$flag['mobile'];
    //     //获取开始日历的第一天和提交日期的当天
    //     $date=date('Y-m-d H:i:s',$flag['time']);
    //     $firstday=date('Y-m-1',$flag['time']);
    //     // 如果是每个月的1号，就转换为上个月全月的日历
    //     if(date('d',$flag['time']=='01')){
    //         $date=date('Y-m-d',$flag['time']);
    //         $firstday=date('Y-m-1',strtotime($date.' -1 day'));
    //     }
    //     // 获取日历月的工作日数组
    //     $workday_arr=(new \yii\db\Query())
    //         ->select('is_work_day')
    //         ->from('wxe_workday')
    //         ->where(['and',['corpid'=>$corpid],['between','date',$firstday,$date]])
    //         ->indexBy('date')
    //         ->column();
    //     // 获取目标员工的考勤信息
    //     $attendance_arr=(new \yii\db\Query())
    //         ->select('attendance')
    //         ->from('wxe_attendance')
    //         ->where(['and',['mobile'=>$mobile],['corpid'=>$corpid],['between','create_at',$firstday,$date]])
    //         ->indexBy('create_at')
    //         ->column();
    //     //初始化返回信息
    //     $month=['01'=>'一','02'=>'二','03'=>'三','04'=>'四','05'=>'五','06'=>'六','07'=>'七','08'=>'八','09'=>'九','10'=>'十','11'=>'十一','12'=>'十二'];
    //     $msg=array('msg' =>'err' , 'nowTime'=>time(),'month'=>$month[date('m',strtotime($date))].'月');
    //     foreach ($workday_arr as $k => $v) {
    //         //处理考勤数据
    //         $w=date('w',strtotime($k));//转换为星期
    //         if(array_key_exists($k, $attendance_arr)){// 日历中的键在考勤数组中存在，说明有当天考勤信息
    //             $msg['attendance'][]=['date'=>$k,'is_work_day'=>$v,'w'=>$w,'attendance'=>$attendance_arr[$k]];
    //         }else{// 日历中的键不在考勤数组中存在，说明没有当天考勤信息
    //             if($v==1){//如果是工作日，则说明没有考勤
    //                 $msg['attendance'][]=['date'=>$k,'is_work_day'=>$v,'w'=>$w,'attendance'=>2];
    //             }elseif($v==0){//如果不是工作日，不返回考勤信息
    //                 $msg['attendance'][]=['date'=>$k,'is_work_day'=>$v,'w'=>$w];
    //             }else{//如果值不是1或0，则工作日数据有错误
    //                 $msg['err']=[$k.':is_work_day is err!'];
    //             }
    //         }
    //     }
    //     if($msg['msg']=='err' && !empty($msg['attendance'])){
    //         $msg['msg']='ok';
    //         echo json_encode($msg,true);
    //     }
    // }
    // /**
    //  * 处理表示解密，还原为时间戳和手机号码
    //  * @param  [type] $flag [加密字符串]
    //  * @return [type] $arr [时间戳和手机号]
    //  */
    // private function decode($flag){
    //     if($flag==''){
    //         return false;
    //     }
    //     $arr['time']=base_convert(substr($flag, 0,6),36,10);
    //     $arr['mobile']=base_convert(substr($flag, -7),36,10);
    //     return $arr;
    // }
    // /**
    //  * 进出电子围栏
    //  * @param  [int] $attendance_id [考勤id]
    //  * @param  [int] $pass          [0：出电子围栏，1：进电子围栏]
    //  */
    // private function inOut($attendance_id,$time,$pass){
    //     // $model = new fence();
    //     // $model->attendance_id=$attendance_id;
    //     // $model->create_at=date('Y-m-d H:i:s',$time);
    //     // $model->pass=$pass;
    //     // $model->save();
    // }
    // /**
    //  * 通过部门id和企业号id获取考勤的开始时间
    //  * @param  [string] $department_ids [部门ids，对应多个部门]
    //  * @param  [string] $corpid         [企业号id]
    //  * @return [数组]                 [经纬度、考勤开始时间数组]
    //  */
    // private function getStartTime($department_ids,$corpid,$time){
    //     if(strpos($department_ids,';')){
    //         $arr=explode(';', $department_ids);
    //         $department_id=$arr[0];
    //    }else{
    //         $department_id=$department_ids;
    //    }
    //    $config=configuration::getConfiguration($department_id,$corpid);
    //    if((int)date('H',$time)<12){
    //         return $config['attendance_start_time'];
    //    }else{
    //         return $config['attendance_rest_end_time'];
    //    }
    // }
    // /**
    //  * 通过部门id和企业号id获取考勤的结束时间
    //  * @param  [string] $department_ids [部门ids，对应多个部门]
    //  * @param  [string] $corpid         [企业号id]
    //  * @return [数组]                 [经纬度、考勤开始时间数组]
    //  */
    // private function getEndTime($department_ids,$corpid,$time){
    //     if(strpos($department_ids,';')){
    //         $arr=explode(';', $department_ids);
    //         $department_id=$arr[0];
    //    }else{
    //         $department_id=$department_ids;
    //    }
    //    $config=configuration::getConfiguration($department_id,$corpid);
    //    if((int)date('H',$time)<=13){
    //         return $config['attendance_rest_start_time'];//返回休息开始时间，即上午下班
    //    }else{
    //         return $config['attendance_end_time'];//否则返回下午下班
    //    }
    // }
    // /**
    //  * 添加一条考勤信息
    //  * @param [string] $mobile     手机号码
    //  * @param [int] $attendance [考勤状态]
    //  * @param [type] $corpid     [description]
    //  * @return int $model-id 返回添加考勤id
    //  */
    // private function addAttendance($mobile,$attendance,$corpid,$time){
    //     $model=attendance::find()->where(['mobile'=>$mobile,'create_at'=>date('Y-m-d',$time)])->one();
    //     // 没有当天的考勤数据则插入一条数据
    //     if($model===null){
    //         $model=new attendance();
    //         $model->mobile=$mobile;
    //         $model->create_at=date('Y-m-d',$time);
    //         if((int)date('H',$time)<=13){//如果是上午的第一次提交考勤
    //             $model->attendance=$attendance;
    //         }else{//如果是下午的第一次提交考勤，说明上午没有考勤，考勤记录为迟到早退
    //             $model->attendance=0;
    //         }
    //         $model->corpid=$corpid;
    //         if($attendance==0){
    //             $model->remarks=1;
    //         }
    //     }elseif($model->attendance <3){
    //         // 如果当天没有请假和休假，而且已经有当天的考勤信息，修改模型数据
    //         if($attendance==0){//如果迟到早退1次，自增1。
    //             $model->remarks+=1;
    //         }
    //         $model->attendance=$attendance;
    //         $model->kuanggong=0;//下午考勤，则旷工归0
    //     }
    //     $model->save();
    //     return $model->id;
    // }
    // /**
    //  * 早退修改考勤的状态
    //  * @param  [string] $mobile 手机号码
    //  */
    // private function updateAttendance($mobile,$date){
    //     $model= attendance::find()->where(['mobile'=>$mobile,'create_at'=>date('Y-m-d',$date)])->one();
    //     // 如果之前是全勤，早退后修改为迟到早退
    //     if($model===null){
    //         die('empty model!');
    //     }elseif($model->attendance <3){
    //         // 如果没有请假和休假则修改模型
    //         $model->attendance=0;
    //         $model->remarks+=1;
    //         $model->save();
    //     }
        
    // }
    // /**
    //  * 根据部门id和corpid构造查询条件(并且status=2 或者 update_at 与目前时间相比超过20分钟)
    //  * @param  [string] $department [部门字符串]
    //  * @param  [string] $corpid     [企业id]
    //  * @param sting $moblie [发送get请求的用户的手机号码]
    //  * @return [array]  $where     [条件数组]
    //  */
    // private function getWhere($department,$corpid,$mobile){
    //     if(strpos($department, ';')){
    //        // 对应多个部门
    //         $where[]='and';
    //         $where[]=['corpid'=>$corpid];
    //         $where[]=['or',['state'=>2],['mobile'=>$mobile],['<','update_at',date('Y-m-d H:i:s',time()-1200)]];
    //         $in[]='in';
    //         $in[]='department';
    //         $arr=explode(';', $department);
    //         $department_ids[]=$department;//先将自己的部门id存进数组
    //         //遍历部门数组的子部门并合到数组内
    //         foreach ($arr as $v) {
    //             $department_ids=array_merge($department_ids,$this->getDepartment($v,$corpid));
    //         }
    //         $in[]=$department_ids;
    //         $where[]=$in;
    //     }else{
    //         //只对应一个部门
    //         $department_ids=$this->getDepartment($department,$corpid);
    //         $where=['and',['corpid'=>$corpid],['in','department',$department_ids],['or',['state'=>2],['mobile'=>$mobile],['<','update_at',date('Y-m-d H:i:s',time()-1200)]]];
    //     }
    //     return $where;
    // }
    // /**
    //  * 获取子部门的id
    //  * @return [string] 子部门id
    //  */
    // private function getDepartment($department_id,$corpid){
    //     $path=(new \yii\db\Query())
    //             ->select(['path'])
    //             ->from('wxe_department')
    //             ->where(['and',['corpid'=>$corpid],['department_id'=>$department_id]])
    //             ->scalar();
    //     $department_ids=(new \yii\db\Query())
    //             ->select(['department_id'])
    //             ->from('wxe_department')
    //             ->where(['and',['corpid'=>$corpid],['like','path',$path]])
    //             ->indexBy('department_id')
    //             ->column();
    //     return $department_ids;
    // }

    // /**
    //  * 将查询的数据转换为合适的结构
    //  * @param  [array] $users  [搜索出的user数组]
    //  * @param  [string] $corpid [企业id]
    //  * @return [array]         [符合要求的数组格式]
    //  */
    // private function transform($users,$corpid){
    //     //获取部门id和部门路径的键值对数组
    //     $departments=(new \yii\db\Query())
    //             ->select(['path'])
    //             ->from('wxe_department')
    //             ->where(['corpid'=>$corpid])
    //             ->indexBy('department_id')
    //             ->column();
    //     // 判断当前是否是工作时间
    //     $is_work_time='no';
    //     $workday=workday::find()->select('is_work_day')->where(['date'=>date('Y-m-d')])->asArray()->one();
    //     if($workday['is_work_day']==1){
    //         $config=configuration::getConfiguration(1,$corpid);
    //         $m_start=strtotime(date('Y-m-d').$config['attendance_start_time']);
    //         $m_end=strtotime(date('Y-m-d').$config['attendance_rest_start_time']);
    //         $a_start=strtotime(date('Y-m-d').$config['attendance_rest_end_time']);
    //         $a_end=strtotime(date('Y-m-d').$config['attendance_end_time']);
    //         if(($m_start<= time() and time() <= $m_end ) or ($a_start<= time() and time() <=$a_end)){
    //             $is_work_time="yes";
    //         }
    //     }
    //     //替换数组中的部门id为部门路径,修改考勤状态
    //     foreach ($users as $k => $v) {
    //         $department_ids=$v['department_path'];
    //         //替换数组的值
    //         if(strpos($department_ids,';')){
    //             //如果是对应多个部门
    //             $id_arr=explode(';',$department_ids);
    //             $users[$k]['department_path']='';
    //             foreach ($id_arr as $id) {
    //                 $users[$k]['department_path'].='['.$departments[$id].'];';
    //             }
    //             $users[$k]['department_path']=rtrim($users[$k]['department_path'],';');
    //         }else{
    //             $users[$k]['department_path']='['.$departments[$v['department_path']].']';
    //         }
    //         // 如果是休息时间，则将状态修改为3，如果是工作时间，则判断是否异常
    //         if($is_work_time=='no'){
    //             $users[$k]['state']=3;
    //         }else{
    //             //如果与现在时间比大于20分钟，则设置为未考勤状态
    //             if($v['update_at']<date('Y-m-d H:i:s',time()-1200)){
    //                 $users[$k]['state']=0;
    //             }
    //         }
    //     }
    //     return $users;
    // }
    // private function updateState($state,$time,$mobile){
    //     Yii::$app->db->createCommand()
    //             ->update('wxe_department_user',['state'=>$state,'update_at'=>date('Y-m-d H:i:s',$time)],"mobile={$mobile}")
    //             ->execute();
    // }
    // public function actionEncode($m){
    //     if($m==''){
    //         return false;
    //     }
    //     $time=strtotime('2017-6-28 8:11:22');
    //     var_dump($time);
    //     $string=base_convert($time,10,36);
    //     $string.=base_convert($m,10,36);
    //     echo $string;
    //     // var_dump(time());
    //     // echo "小时";
    //     // var_dump((int)date('H'));
    // }
}
