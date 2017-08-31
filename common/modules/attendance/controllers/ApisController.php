<?php
namespace common\modules\attendance\controllers;
use common\modules\attendance\models\configuration;
use common\modules\attendance\models\fence;
use common\modules\attendance\models\user;
use common\modules\attendance\models\NewAttendance;
use common\modules\attendance\models\department;
use common\modules\attendance\models\workday;
use Yii;
class ApisController extends \yii\web\Controller{
	public $enableCsrfValidation = false;
	/**
     * 返回配置信息
     *请求地址 http://gs-admin.buy027.com/index.php?r=attendance%2Fapis/config&flag=os8duy7c5v4xs
     * @param  [type] $mobile [手机号码]
     * @return [type] json [配置信息]
     */
    public function actionConfig($flag)
    {
        header("Content-type: text/html; charset=utf-8"); 
        //获取企业id（corpid）
        $corpid=Yii::$app->request->get('corpid','ww1de94f786c71be9f');
        //获取手机号码和时间戳数组
        $flag=$this->decode($flag);
        //初始化配置数组
    	$configuration['msg']='err';
        $configuration['isWorkDay']=configuration::isWorkDay($corpid);//查询当天是否是工作日
        $configuration['nowtime']=time();

    /****如果传递的数据符合条件，则进行配置参数查询*****/
        if($flag && is_numeric($flag['mobile']) &&strlen($flag['mobile'])==11){
            //修改配置数组
        	$configuration['msg']='ok';
            //根据cordid查询配置信息的time时间戳
            $time=configuration::getTime($corpid);
            //比对$flag['time']与配置信息更改的时间戳,如果不一致，查询手机号码所对应的部门id。
            if($time!=$flag['time']){
                $department_id=configuration::getDepartmentId($flag['mobile']);
                //再根据部门id和cordid查询配置信息，如果是多部门的，说明是领导（分管多个部门）返回总部信息，否则直接返回职工所在部门的配置信息
                if(strstr($department_id,',')==false){
                    $configuration['config']=configuration::getConfiguration($department_id,$corpid);
                }else{
                    $configuration['config']=configuration::getConfiguration(1,$corpid);
                }
            }
        }
        //转换为json数组，输出
        return  json_encode($configuration,true);
    }
    /**
     * 提交进出电子围栏信息,只对应attendance和fence表，对应统计功能
     *请求地址 http://gs-admin.buy027.com/index.php?r=attendance%2Fapis/fence
     * @param [type] $a [进出电子围栏信息]
     * @param [type] $flag [标识信息]
     * @return [type] [description]
     */

    public function actionFence(){
        header("Content-type: text/html; charset=utf-8"); 
        //获得提交数据
        $post=Yii::$app->request->post();
        if(!isset($post['flag'])){
            return ('no post data!');
        }
        //解码处理数据结构，获得需要的数据变量
        $flag=$this->decode($post['flag']);
        $list=json_decode($post['list'],true);
        $mobile=$flag['mobile'];
        //初始化返回信息数组
        $msg = array('msg' =>'err' , 'time'=>time());
        //如果提交的数据有效,则新增fence模型的数据，修改实时状态信息
        if($list && is_numeric($mobile) && strlen($mobile)==11){
            //user不存在，直接返回错误
            $user=user::find()->where(['mobile'=>$mobile])->asArray()->one();
            if($user==null){
                $msg['status']='电话号码无效!';
                return json_encode($msg);
            }
            foreach ($list as $k => $v) {
                // 判断提交的数据时间不是工作日，直接跳出循环，app没做判断，所以只有这里判断
                $date=date('Y-m-d',$v['time']);
                $workday=workday::find()->where(['date'=>$date,'is_work_day'=>0])->asArray()->one();
                if($workday){
                	$msg['status']='非工作日!';
                    continue;
                }
                if(date('H',$v['time'])>13){
                	$am_pm=2;
                }else{
                	$am_pm=1;
                }
                $model=NewAttendance::find()->where(['mobile'=>$mobile,'work_date'=>date('Y-m-d',$v['time']),'am_pm'=>$am_pm])->one();
                // 如果模型存在，而且不是迟到早退、全勤、旷工，则跳出循环
                if($model && $model->attendance > 2){
                	$msg['status']='已经请假或其它!';
                    break;
                }
                // 根据状态值，分别进行考勤管理
                if($v['a']==1){//上班
                    //插入一条attention数据（考勤信息）1代表是全勤，0代表迟到早退
                    $time=$this->getStartTime($user['department'],$v['time']);//获取配置信息中的考勤开始时间
                    $start_time=date('Y-m-d',$v['time']).' '.$time[0];//当日考勤开始时间
                    $attendance=1;
                    if($v['time']>(strtotime($start_time))){
                        $attendance=0;//如果晚点超过偏移值到达电子围栏，则是迟到
                    }
                    $attendance_id=$this->addAttendance($mobile,$attendance,$v['time'],$am_pm);
                    if($attendance){
                    	//插入一条进入电子围栏的上班信息 5=>按时上班
                    	$this->inOut($attendance_id,$v['time'],5);
                    }else{
                    	//插入一条进入电子围栏的上班信息 4=>迟到
                    	$this->inOut($attendance_id,$v['time'],4);
                    }
                    //修改实时考勤状态为上班状态
                    $this->updateState(1,time(),$mobile);
                    $msg['status']='Go to the office!';
                }elseif ($v['a']==3) {//早退，修改attention表中的全勤信息为不是全勤
                    $attendance_id=$this->updateAttendance($mobile,$v['time'],$am_pm,$user['department']);
                    //插入一条从电子围栏 3=早退 的信息
                    if($attendance_id>0){
                        $this->inOut($attendance_id,$v['time'],3);
                    }
                    $msg['status']='Leave early!';
                }elseif ($v['a']==4) {//班中进电子围栏
                    //班中进电子围栏,先获得获得考勤id
                    if($model){
                    	//班中进电子围栏,插入一条进电子围栏信息 1=班中进
                    	$this->inOut($model->id,$v['time'],'1');
                    	$msg['status']='Come in!';
                    }else{
                    	\yii::error([$_POST,'no attendance ,come in fence err!']);
                    }
                    //修改实时考勤状态为在单位的状态
                    $this->updateState(1,time(),$mobile);
                   
                }elseif($v['a']==5){//班中出电子围栏
                    //班中出电子，查找当天当人的考勤id
                    if($model){//模型存在则插入进出电子围栏信息
						//插入一条出电子围栏的信息 0=>班中出
						$this->inOut($model->id,$v['time'],0);
						$msg['status']='Come out!';
                    }else{//模型不存在，则在日志输出错误信息
                    	\yii::error([$_POST,'no attendance ,come out fence err!']);
                    }
                    //修改实时考勤状态为不在单位的状态
                    $this->updateState(2,time(),$mobile);
                }elseif($v['a']==6){//按时下班
                    //修补漏洞补丁：app发送完下午上班，又发送上午下班的考勤
                    $end_arr=$this->getEndTime($user['department'],$v['time']);//获取配置信息中的考勤结束时间
                    $end_time=date('Y-m-d',$v['time']).' '.$end_arr[0];//当日考勤结束时间
                    $end_time=strtotime($end_time)-$end_arr[1]*60;
                    if($v['time']<$end_time && $am_pm==2){//下午提交上午的下班时间
                        $model=NewAttendance::find()->where(['mobile'=>$mobile,'work_date'=>date('Y-m-d',$v['time']),'am_pm'=>1])->one();
                        if($model===null){//模型不存在，说明没有上午上班考勤
                            $model=new NewAttendance();
                            $model->mobile=$mobile;
                            $model->work_date=date('Y-m-d',$v['time']);
                            $model->am_pm=1;
                            $model->attendance=0;
                            $model->num=1;
                        }else{
                            $time=$this->getStartTime($user['department'],strtotime(date('Y-m-d',$v['time']).' 10:00:00'));//获取配置信息中的考勤开始时间
                            $start_time=date('Y-m-d',$v['time']).' '.$time[0];//当日上午考勤开始时间
                            if($model->start_at<strtotime($start_time)){//正常上班
                                $model->attendance=1;
                                $model->num=0;
                            }else{//迟到
                                $model->attendance=0;
                                $model->num=1;
                            }
                        }
                        $model->end_at=$v['time'];
                        $model->save();
                        continue;
                    }
                    //补丁：如果是第二天提交昨天下午的下班考勤
                    if($v['time']>$end_time && $am_pm==2 && date('Y-m-d',$v['time'])<date('Y-m-d')){
                        $model=NewAttendance::find()->where(['mobile'=>$mobile,'work_date'=>date('Y-m-d',$v['time']),'am_pm'=>$am_pm])->one();
                        $time=$this->getStartTime($user['department'],$v['time']);//获取配置信息中的考勤开始时间
                        $start_time=date('Y-m-d',$v['time']).' '.$time[0];//前一天考勤考勤开始时间
                        $start_time=strtotime($start_time);
                        if($model){//如果模型存在，存在上班考勤
                            if(!empty($model->start_at) && $model->start_at<=$start_time){
                                //如果正常上班
                                $model->attendance=1;
                                $model->num=0;
                            }else{//如果迟到则
                                $model->attendance=0;
                                $model->num=1;
                            }
                        }else{//模型为空，就是没有考勤信息则
                            $model=new NewAttendance();
                            $model->mobile=$mobile;
                            $model->work_date=date('Y-m-d',$v['time']);
                            $model->am_pm=1;
                            $model->attendance=0;
                            $model->num=1;
                        }
                        $model->end_at=$v['time'];
                        $model->save();
                        continue;
                    }
                    //正常流程
                    $time=$this->getStartTime($user['department'],$v['time']);//获取配置信息中的考勤开始时间
                    $start_time=date('Y-m-d',$v['time']).' '.$time[0];//考勤开始时间
                    if($model){//模型存在，说明上班正常考勤，正常提交下班的情况下，下班时间提交考勤重新修改模型
                        if($model->start_at < strtotime($start_time)){
                    		$model->attendance=1;
                    		$model->num=0;
                    	}else{
                    		$model->attendance=0;
                    		$model->num=1;
                    	}
                    	$model->end_at=$v['time'];
                    	//正常下班，插入一条出电子围栏的信息 2=按时下班
                    	$this->inOut($model->id,$v['time'],2);
	                    $msg['status']='Come off duty!';
                    }else{//如果模型不存在，说明上班时间没有考勤，默认为迟到一次。
                        $model=new NewAttendance();
                        $model->mobile=$mobile;
                        $model->work_date=date('Y-m-d',$v['time']);
                        $model->am_pm=$am_pm;
                        $model->attendance=0;
                        $model->end_at=$v['time'];
                        $model->num=1;
                    }
                    $model->save();
                    //修改实时考勤状态为上班状态
	                $this->updateState(1,time(),$mobile);
                }elseif($v['a']==7){
                    //将手机长时间停放在一个地方的异常信息保存到state
                    if($model){
                        $model->state=1;
                        $model->save();
                    }
                }elseif($v['a']==10){//手动考勤上班
                	if($model===null){
                		//模型不存在，处理手动的上班考勤
                		$time=$this->getStartTime($user['department'],$v['time']);//获取配置信息中的考勤开始时间
		                $start_time=date('Y-m-d',$v['time']).' '.$time[0];//当日考勤开始时间
                        $end_time=$this->getEndTime($user['department'],$v['time']);//获取配置信息中的考勤结束时间
                        $end_time=date('Y-m-d',$v['time']).' '.$end_time[0];//当日考勤结束时间
		                if($v['time']<(strtotime($start_time)-3600)){
		                	//没有 到考勤时间（上班前1小时）
		                	$msg['status']='还没有到考勤时间!';
		                	return json_encode($msg);
		                }elseif($v['time']>(strtotime($end_time))){
                            $msg['status']='已经过了打卡时间!';
                            return json_encode($msg);
                        }else{
		                	$attendance=new NewAttendance();
		                	$attendance->attendance=1;
		                    if($v['time']>(strtotime($start_time))){
		                        $attendance->attendance=0;//如果晚点到达电子围栏，则是迟到
		                        $attendance->num=1;
		                    }
		                    $attendance->work_date=date('Y-m-d',$v['time']);
		                    $attendance->mobile=$mobile;
		                    $attendance->am_pm=$am_pm;
		                    $attendance->start_at=$v['time'];
		                   	$attendance->save();
                    		if($attendance->attendance){
		                    	//插入一条进入电子围栏的上班信息 5=>按时上班
		                    	$this->inOut($attendance->id,$v['time'],5);
		                    }else{
		                    	//插入一条进入电子围栏的上班信息 4=>迟到
		                    	$this->inOut($attendance->id,$v['time'],4);
		                    }
		                   	$msg['status']='上班打卡成功!';
		                }
		                
                	}else{
                        $msg['msg']='';
                		$msg['status']='已经打过卡!';
                	}
                	
                }elseif($v['a']==11){
                	$time=$this->getEndTime($user['department'],$v['time']);//获取配置信息中的考勤结束时间
	                $end_time=date('Y-m-d',$v['time']).' '.$time[0];//当日考勤结束时间
	                if($v['time']>(strtotime($end_time)+3600)){//下班1小时内不在电子围栏手动打考勤，给出提示
	                	$msg['status']='已经过了打卡的时间!';
                        $msg['msg']='';
	                	continue;
	                }
	                $state=1;//默认考勤状态是全勤
                    if($v['time']<(strtotime($end_time)-$time[1]*60)){
                        $state=0;//如果晚点超过偏移值离开电子围栏，则是早退
                    }
                	if($model===null){
                		//模型不存在，处理手动的下班考勤
                		$attendance=new NewAttendance();
                		$attendance->attendance=0;
                		if($state===0){
                			$attendance->num=2;
                		}else{
                			$attendance->num=1;
                		}
	                    $attendance->work_date=date('Y-m-d',$v['time']);
	                    $attendance->mobile=$mobile;
	                    $attendance->am_pm=$am_pm;
	                    $attendance->end_at=$v['time'];
	                   	$attendance->save();
	                   	if($state){
	                   		//插入一条进入电子围栏的上班信息 2=>按时下班
                    		$this->inOut($attendance->id,$v['time'],2);
	                   	}else{
	                   		//插入一条进入电子围栏的上班信息 3=早退
                    		$this->inOut($attendance->id,$v['time'],3);
	                   	};
	                   	$msg['status']='下班打卡成功!';
                	}else{
            			$time=$this->getStartTime($user['department'],$v['time']);//获取配置信息中的考勤开始时间
               		 	$start_time=date('Y-m-d',$v['time']).' '.$time[0];//当日考勤开始时间
               		 	if($model->start_at <= strtotime($start_time)){
               		 		// 如果上班考勤正常，且考勤状态为1=全勤
               		 		$model->attendance=$state;
               		 		if($state==0){
               		 			$model->num=1;
               		 		}else{
               		 			$model->num=0;
               		 		}
               		 	}else{
               		 		if($state){
               		 			$model->num=1;
               		 		}else{
               		 			$model->num=2;// 上班迟到，且下班早退
               		 		}
               		 	}
                		$model->end_at=$v['time'];
                		$model->save();
                		$msg['status']='下班打卡成功!';
                		if($state){
	                   		//插入一条进入电子围栏的上班信息 2=>按时下班
                    		$this->inOut($model->id,$v['time'],2);
	                   	}else{
	                   		//插入一条进入电子围栏的上班信息 3=早退
                    		$this->inOut($model->id,$v['time'],3);
	                   	};
                	}
                }
            }
            if($msg['msg']==''){
                $msg['msg']='err';
            }else{
               $msg['msg']='ok'; 
            }
        }
        return json_encode($msg);
    }
    /**
     * 获取个人的考勤状态
     *http://gs-admin.buy027.com/index.php?r=attendance%2Fapis/today&flag=osb16m7c5v4xs
     *@param $flag 个人信息标识
     *@return json 今日考勤状态
     */
    public function actionToday(){
    	header("Content-type: text/html; charset=utf-8");
    	$flag=Yii::$app->request->get('flag',0);
    	$msg=array('msg' =>'err' , 'time'=>time());
    	//无flag参数，则返回错误信息
    	if($flag===0){
    		$msg['status']='telephone is empty!';
    		return json_encode($msg);
    	}

        // 解析出手机号码和时间
    	$flag=$this->decode($flag);
    	$time=$flag['time'];
    	$mobile=$flag['mobile'];
    	$user=user::find()->select(['id','name'])->where(['mobile'=>$mobile])->asArray()->one();
    	// 用户表中无信息，则返回错误信息
    	if($user==null){
    		$msg['status']='电话号码无效!';
    		return json_encode($msg);
    	}
    	$msg['name']=$user['name'];
    	$msg['msg']='ok';
    	//如果是非工作日,返回休息日
        $workday=workday::find()->select('id')->where(['date'=>date('Y-m-d'),'is_work_day'=>0])->asArray()->one();
        if($workday){
            $msg['status']=0;
            $msg['am_work_state']='休息日';
            $msg['am_off_state']='休息日';
            $msg['pm_work_state']='休息日';
            $msg['am_off_state']='休息日';
            return json_encode($msg);
        }
    	// 查找当日考勤信息
    	$models=NewAttendance::find()->where(['mobile'=>$mobile,'work_date'=>date('Y-m-d',$time)])->all();
    	//循环处理模型，组成数组字符串
    	foreach ($models as  $model) {
    		if($model->am_pm==1){
    			if($model->attendance >2){
    				$msg['status']=1;
    				$msg['am_work_state']=$this->getToday($model);
    				$msg['am_off_state']=$msg['am_work_state'];
    			}else{
    				$msg['status']=1;
	    			if($model->start_at){
	    				$msg['am_work_time']=date('Y-m-d H:i:s',$model->start_at);
	    				$msg['am_work_state']=$this->getToday($model);
	    			}
	    			if($model->end_at){
	    				$msg['am_off_time']=date('Y-m-d H:i:s',$model->end_at);
	    				$msg['am_off_state']=$this->getToday($model);
	    			}
    			}
    			
    		}elseif($model->am_pm==2){
    			if($model->attendance >2){
    				$msg['status']=1;
    				$msg['pm_work_state']=$this->getToday($model);
    				$msg['pm_off_state']=$msg['pm_work_state'];
    			}else{
    				$msg['status']=1;
	    			if($model->start_at){
	    				$msg['pm_work_time']=date('Y-m-d H:i:s',$model->start_at);
	    				$msg['pm_work_state']=$this->getToday($model);
	    			}
	    			if($model->end_at){
	    				$msg['pm_off_time']=date('Y-m-d H:i:s',$model->end_at);
	    				$msg['pm_off_state']=$this->getToday($model);
	    			}
    			}
    		}
    	}
		if(!isset($msg['am_work_state'])){
			$msg['am_work_state']='无';
		}
		if(!isset($msg['am_off_state'])){
			$msg['am_off_state']='无';
		}
    	if(!isset($msg['pm_work_state'])){
			$msg['pm_work_state']='无';
		}
		if(!isset($msg['pm_off_state'])){
			$msg['pm_off_state']='无';
		}
		if(!isset($msg['status'])){
			$msg['status']=1;
		}
    	return json_encode($msg);
    }
    /**
    * @param $model 考勤模型
    * @ return $state 状态
    */
    private function getToday($model){
		if($model->attendance<2){
			$state='打卡成功';
		}elseif($model->attendance==3){
			$state='事假';
		}elseif($model->attendance==4){
			$state='病假';
		}elseif($model->attendance==5){
			$state='公休';
		}elseif($model->attendance==6){
			$state='出差';
		}elseif($model->attendance==7){
			$state='外出';
		}else{
			$state='其它';
		}
    	return $state;
    }
    /**
     * 获取在电子围栏之外的或者未考勤的部门员工情况【0=>异常，1=>在单位，2=>不在单位,3=休息时间】
     * 地址 http://gs-admin.buy027.com/index.php?r=attendance%2Fapis/status&flag=os8duy7c5v4xs
     *@param [type] $flag [标识信息]
     * @return [type] [json]
     */
    public function actionStatus(){
        header("Content-type: text/html; charset=utf-8"); 
        //获得提交参数
        $request=Yii::$app->request;
        $flag=$request->get('flag');
        $corpid=$request->get('corpid','ww1de94f786c71be9f');//获取企业id（corpid）
        //解码
        $flag=$this->decode($flag);
        $mobile=$flag['mobile'];
        $msg = array('msg' =>'err' , 'time'=>time());
        //如果提交的数据有效
        if($flag && is_numeric($mobile) &&strlen($mobile)==11){
            //如果提交的电话号码在数据库中，查找相应数据返回。
            $user=user::find()->select(['name','department_path'=>'department','leader','update_at','mobile','state','lng','lat'])
                ->where(['mobile'=>$mobile])->asArray()->one();
            if($user==null){
                $msg['msg']='The mobile is invalid!';
                return json_encode($msg);
            }
            //根据权限查询实时信息
            if($user['leader']==10){
                //如果是普通员工
                unset($user['leader']);
                $users[0]=$user;
            }elseif ($user['leader']==5) {
                // 如果是部门领导权限
                $where=['and',['department'=>$user['department_path']],['or',['state'=>2],['mobile'=>$mobile],['<','update_at',date('Y-m-d H:i:s',time()-1200)]]];
                $users=user::find()->select(['name','department_path'=>'department','update_at','mobile','state','lng','lat'])->where($where)->orderBy('leader asc')->asArray()->all();
            }elseif ($user['leader']==0) {
                //如果是最高领导权限
                $users=user::find()->select(['name','department_path'=>'department','update_at','mobile','state','lng','lat'])
                    ->where(['or',['state'=>2],['mobile'=>$mobile],['<','update_at',date('Y-m-d H:i:s',time()-1200)]])
                    ->orderBy('userid')->asArray()->all();
            }
            $msg['status']=$this->transform($users,$corpid);
            $msg['msg']='ok';
        }
        return json_encode($msg,true);
    }
    /**
     * 将查询的数据转换为合适的结构
     * @param  [array] $users  [搜索出的user数组]
     * @param  [string] $corpid [企业id]
     * @return [array]         [符合要求的数组格式]
     */
    private function transform($users,$corpid){
        //获取部门id和部门路径的键值对数组
        // $departments=(new \yii\db\Query())->select(['path'])->from('wxe_department')->indexBy('department_id')->column();
        // 判断当前是否是工作时间
        $is_work_time='no';
        $workday=workday::find()->select('is_work_day')->where(['date'=>date('Y-m-d')])->asArray()->one();
        if($workday['is_work_day']==1){
            $config=configuration::getConfiguration(1,$corpid);
            $m_start=strtotime(date('Y-m-d').$config['attendance_start_time']);
            $m_end=strtotime(date('Y-m-d').$config['attendance_rest_start_time']);
            $a_start=strtotime(date('Y-m-d').$config['attendance_rest_end_time']);
            $a_end=strtotime(date('Y-m-d').$config['attendance_end_time']);
            if(($m_start<= time() and time() <= $m_end ) or ($a_start<= time() and time() <=$a_end)){
                $is_work_time="yes";
            }
        }
        //替换数组中的部门id为部门路径,修改考勤状态
        foreach ($users as $k => $v) {
            // $department_ids=$v['department_path'];
            // //替换数组的值
            // if(strpos($department_ids,';')){
            //     //如果是对应多个部门
            //     $id_arr=explode(';',$department_ids);
            //     $users[$k]['department_path']='';
            //     foreach ($id_arr as $id) {
            //         $users[$k]['department_path'].='['.$departments[$id].'];';
            //     }
            //     $users[$k]['department_path']=rtrim($users[$k]['department_path'],';');
            // }else{
            //     $users[$k]['department_path']='['.$departments[$v['department_path']].']';
            // }
            // 如果是休息时间，则将状态修改为3，如果是工作时间，则判断是否异常
            if($is_work_time=='no'){
                $users[$k]['state']=3;
            }else{
                //如果与现在时间比大于20分钟，则设置为未考勤状态
                if($v['update_at']<date('Y-m-d H:i:s',time()-1200)){
                    $users[$k]['state']=0;
                }
            }
        }
        return $users;
    }
    /**
     * 每过10分钟发一次定位信息,
     *@param [type] $p[经纬度]
     *@param [type] $flag [标识信息]
     * @return [type] [description]
     */
    public function actionPosition(){
        header("Content-type: text/html; charset=utf-8"); 
        //获得提交参数
        $request=Yii::$app->request;
        $lng=$request->get('lng',0);
        $lat=$request->get('lat',0);
        $state=$request->get('state',1);
        $flag=$request->get('flag');
        //解码
        $flag=$this->decode($flag);
        $time=$flag['time'];
        $mobile=$flag['mobile'];
        //初始化返回信息
        $msg = array('msg' =>'err' , 'time'=>time());
        //如果提交的数据有效
        if($flag && is_numeric($mobile) &&strlen($mobile)==11){
            //user不存在，直接返回错误
            $user=user::find()->select('id')->where(['mobile'=>$mobile])->asArray()->one();
            if($user==null){
                $msg['status']='The mobile is invalid!';
                return json_encode($msg);
            }
            Yii::$app->db->createCommand()
                ->update('wxe_department_user',['lat'=>$lat,'lng'=>$lng,'state'=>$state,'update_at'=>date('Y-m-d H:i:s',$time)],"id={$user['id']}")
                ->execute();
            $msg['msg']='ok';
            $msg['status']='update success!';
        }
        return json_encode($msg);
    }
    /**
     * 获取指定人员指定月份的考勤信息
     * http://gs-admin.buy027.com/index.php?r=attendance%2Fapis/attendance&flag=oscvum7c5v4xs
     * @return [json] [考勤的字符串]
     */
    public function actionAttendance(){
        header("Content-type: text/html; charset=utf-8"); 
        //获得提交参数
        $request=Yii::$app->request;
        $flag=$request->get('flag');
        //解码
        $flag=$this->decode($flag);
        $mobile=$flag['mobile'];
        //获取开始日历的第一天和提交日期的当天
        $date=date('Y-m-d',$flag['time']);
        $firstday=date('Y-m-1',$flag['time']);

        // 获取日历月的工作日数组
        $workday=workday::find()->select(['id','date','is_work_day'])->where(['between','date',$firstday,$date])->asArray()->all();
        foreach ($workday as  $v) {
        	$workday_arr[$v['date'].'@1']=$v['is_work_day'];
        	$workday_arr[$v['date'].'@2']=$v['is_work_day'];
        }
        // 获取目标员工的考勤信息
        $attendance=NewAttendance::find()->where(['and',['mobile'=>$mobile],['between','work_date',$firstday,$date]])->asArray()->all();
        foreach ($attendance as  $value) {
            //如果没有打下班考勤，虽然是全勤也要返回迟到早退
            if($value['attendance']==1 && !isset($value['end_at']) && date('Y-m-d')> $date){
                $attendance_arr[$value['work_date'].'@'.$value['am_pm']]=0;
            }else{
                $attendance_arr[$value['work_date'].'@'.$value['am_pm']]=$value['attendance'];
            }
        }
        //初始化返回信息
        $month=['01'=>'一','02'=>'二','03'=>'三','04'=>'四','05'=>'五','06'=>'六','07'=>'七','08'=>'八','09'=>'九','10'=>'十','11'=>'十一','12'=>'十二'];
        $msg=array('msg' =>'err' , 'nowTime'=>time(),'month'=>$month[date('m',strtotime($date))].'月');
        //获取考勤状态数组
        $state=NewAttendance::attendance();
        foreach ($workday_arr as $k => $v) {
            //处理考勤数据
            $w=date('w',strtotime(strstr($k,'@',true)));//转换为星期
            if(strstr($k,'@')=='@1'){
            	$am_pm_state='amState';
            }else{
            	$am_pm_state='pmState';
            }
            if(isset($attendance_arr) && array_key_exists($k, $attendance_arr)){
            // 日历中的键在考勤数组中存在，说明有当天考勤信息
            	if(!isset($msg['attendance'][strstr($k,'@',true)])){
            		$msg['attendance'][strstr($k,'@',true)]=['date'=>strstr($k,'@',true),'is_work_day'=>$v,'w'=>$w];
            	}
                $msg['attendance'][strstr($k,'@',true)][$am_pm_state]=$state[$attendance_arr[$k]];
            }else{// 日历中的键不在考勤数组中存在，说明没有当天考勤信息
                if($v==1){//如果是工作日，工作日的键不存在考勤日的键中则说明是旷工
                	if(!isset($msg['attendance'][strstr($k,'@',true)])){
            			$msg['attendance'][strstr($k,'@',true)]=['date'=>strstr($k,'@',true),'is_work_day'=>$v,'w'=>$w];
            		}
            		if(strstr($k,'@',true)==date('Y-m-d') && date('H')<15){
            			$msg['attendance'][strstr($k,'@',true)][$am_pm_state]=' ';
            		}else{
            			$msg['attendance'][strstr($k,'@',true)][$am_pm_state]=$state[2];
            		}
                    
                }elseif($v==0){//如果不是工作日，不返回考勤信息
                	if(!isset($msg['attendance'][strstr($k,'@',true)])){
                    	$msg['attendance'][strstr($k,'@',true)]=['date'=>strstr($k,'@',true),'is_work_day'=>$v,'w'=>$w,'amState'=>'休息日','pmState'=>'休息日'];
                	}
                }else{//如果值不是1或0，则工作日数据有错误
                    $msg['status']=[$k.':上午下午的数字值不对!'];
                    return json_encode($msg);
                }
            }
        }

        if(!empty($msg['attendance'])){
        	$msg['attendance']=array_values($msg['attendance']);
        	$msg['status']='Query success!';
            $msg['msg']='ok';
            echo json_encode($msg,true);
        }
    }
    /**
     * 添加一条考勤信息
     * @param [string] $mobile     手机号码
     * @param [int] $attendance [考勤状态]
     * @param $time  发生时间
     * @param $am_pm 上午或下午
     * @return int $model-id 返回添加考勤id
     */
    private function addAttendance($mobile,$attendance,$time,$am_pm){
    	$where=['mobile'=>$mobile,'work_date'=>date('Y-m-d',$time),'am_pm'=>$am_pm];
        $model=NewAttendance::find()->where($where)->one();
        // 没有当天上午（下午）的考勤数据则插入一条数据
        if($model===null){
            $model=new NewAttendance();
            $model->mobile=$mobile;
            $model->work_date=date('Y-m-d',$time);
            $model->am_pm=$am_pm;
            $model->attendance=$attendance;
            if($attendance==0){
            	$model->num=1;
            }
            $model->start_at=$time;
        }elseif($model->attendance <3){
            // 如果当天没有请假和休假，而且已经有当天的考勤信息，修改模型数据
            // $model->attendance=$attendance;
            // $model->start_at=$time;
        }
        $model->save();
        return $model->id;
    }
    /**
     * 早退修改考勤的状态
     * @param  [string] $mobile 手机号码
     * @return int 
     */
    private function updateAttendance($mobile,$time,$am_pm,$user){
        //修补漏洞补丁：app下午发送完上午的的早退考勤
        $end_arr=$this->getEndTime(1,$time);//获取配置信息中的考勤结束时间
        $end_time=date('Y-m-d',$time).' '.$end_arr[0];//当日考勤结束时间
        $end_time=strtotime($end_time)-$end_arr[1]*60;
        if(((int)$time)<$end_time && $am_pm==2){//如果发送的早退时间小于下午的考勤结束时间，说明是上午的
            $model=NewAttendance::find()->where(['mobile'=>$mobile,'work_date'=>date('Y-m-d',$time),'am_pm'=>1])->one();
            if($model){
                $times=$this->getStartTime($user,strtotime(date('Y-m-d',$time).' 11:00:00'));//获取配置信息中的考勤开始时间
                $start_time=date('Y-m-d',$time).' '.$times[0];//当日考勤开始时间
                $model->attendance=0;
                if($model->start_at <= strtotime($start_time)){
                    $model->num=1;
                }else{
                    $model->num=2;
                }
                $model->end_at=$time;
                $model->save();
                return $model->id;
            }
            return 0;
        }
        //正常的处理过程
        $model= NewAttendance::find()->where(['mobile'=>$mobile,'work_date'=>date('Y-m-d',$time),'am_pm'=>$am_pm])->one();
        if($model===null){
            // 如果模型不存在，则不插入数据，没有数据代表旷工。
        }elseif($model->attendance <2){
            // 如果没有请假和休假则修改模型
            $times=$this->getStartTime($user,$time);//获取配置信息中的考勤开始时间
   		 	$start_time=date('Y-m-d',$time).' '.$times[0];//当日考勤开始时间
            //补丁：如果手动考勤打卡正常下班，则早退的打卡直接返回
            if(isset($model->end_at) && $model->attendance==1 && $model->end_at >$end_time){
                return 0;
            }
   		 	if($model->start_at<= strtotime($start_time)){
   		 		$model->num=1;
   		 	}else{
   		 		$model->num =2;
   		 	}
            $model->attendance=0;
            $model->end_at=$time;
            $model->save();
            return $model->id;
        }
        return 0; 
    }
    /**
     * 进出电子围栏
     * @param  [int] $attendance_id [考勤id]
     * @param  [int] $pass          [0：出电子围栏，1：进电子围栏]
     */
    private function inOut($attendance_id,$time,$pass){
        if($attendance_id){
            $model = new fence();
            $model->attendance_id=$attendance_id;
            $model->create_at=date('Y-m-d H:i:s',$time);
            $model->pass=$pass;
            $model->save();  
        }
    }
    /**
    *修改地理位置信息
    */
    private function updateState($state,$time,$mobile){
        Yii::$app->db->createCommand()
                ->update('wxe_department_user',['state'=>$state,'update_at'=>date('Y-m-d H:i:s',$time)],"mobile={$mobile}")
                ->execute();
    }
    /**
     * 通过部门id和企业号id获取考勤的开始时间
     * @param  [string] $department_ids [部门ids，对应多个部门]
     * @param  [string] $corpid         [企业号id]
     * @return [数组]                 [经纬度、考勤开始时间数组]
     */
    private function getStartTime($department_ids,$time){
       if(strpos($department_ids,';')){
            $arr=explode(';', $department_ids);
            $department_id=$arr[0];
       }else{
            $department_id=$department_ids;
       }
       $config=configuration::getConfiguration($department_id);
       if((int)date('H',$time)<=13){
            return [$config['attendance_start_time'],$config['attendanceOffset']];
       }else{
            return [$config['attendance_rest_end_time'],$config['attendanceOffset']];
       }
    }
    /**
     * 通过部门id和企业号id获取考勤的结束时间
     * @param  [string] $department_ids [部门ids，对应多个部门]
     * @param  [string] $corpid         [企业号id]
     * @return [数组]                 [经纬度、考勤开始时间数组]
     */
    private function getEndTime($department_ids,$time){
        if(strpos($department_ids,';')){
            $arr=explode(';', $department_ids);
            $department_id=$arr[0];
       }else{
            $department_id=$department_ids;
       }
       $config=configuration::getConfiguration($department_id);
       if((int)date('H',$time)<=13){
            return [$config['attendance_rest_start_time'],$config['attendanceOffset']];//返回休息开始时间，即上午下班
       }else{
            return [$config['attendance_end_time'],$config['attendanceOffset']];//否则返回下午下班
       }
    }
     /**
     * 处理表示解密，还原为时间戳和手机号码
     * @param  [type] $flag [加密字符串]
     * @return [type] $arr [时间戳和手机号]
     */
    private function decode($flag){
        if($flag==''){
            return false;
        }
        $arr['time']=base_convert(substr($flag, 0,6),36,10);
        $arr['mobile']=base_convert(substr($flag, -7),36,10);
        return $arr;
    }
    public function actionEncode($m){
        if($m==''){
            return false;
        }
        $time=strtotime('2017-8-9 11:50:22');
        var_dump($time);
        $string=base_convert($time,10,36);
        $string.=base_convert($m,10,36);
        echo $string;
    }
}