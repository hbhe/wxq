<?php
namespace common\modules\attendance\controllers;
use yii;
use yii\web\Controller;
use common\modules\attendance\models\AddressBook;
use common\modules\attendance\models\Vacate;
use common\modules\attendance\models\user;
use common\modules\attendance\models\NewAttendance;
use common\modules\attendance\models\workday;
use wechat\models\Wechat;
use wechat\models\QyWechat;
/**
 * 请假考勤的功能
 */
class DefaultController extends Controller
{
	public $enableCsrfValidation = false;
    /**
     * 请假首页
     * @return string
     */
    public function actionIndex()
    {
    	if($_POST){
    		//检查是否有相同的请假存在，如果存在则不保存
    		$post=Yii::$app->request->post();
            if($post['dayOrHalf']=='0'){
                $where=['and',['submitter'=>$post['submitter']],['from_date'=>$post['from_date']],['to_date'=>$post['to_date']]];
            }else{
                //提交人、日期重合，而且请假为全天或者提交的上午（或下午），就是全天请假了，上午下午也不能请假
                $where=['and',['submitter'=>$post['submitter']],['from_date'=>$post['from_date']],['to_date'=>$post['to_date']],['or',['dayOrHalf'=>0],['dayOrHalf'=>$post['dayOrHalf']]]];
            }
            $model=Vacate::find()->select('dayOrHalf')->where($where)->asArray()->one();
            if($model){
                return $this->renderPartial('msg',['msg'=>'已经请过当日的假，不能重复请假']);
            }
    		// 如果model不存在填充模型
            $model=new Vacate();
    		$model->submitter=$post['submitter'];
    		$users=$post['checker'];
            $model->dayOrHalf=$post['dayOrHalf'];
    		if(count($users)==2){
    			$model->reviewer=$users[0];
    			$model->approver=$users[1];
    		}elseif(count($users)==1){
    			$model->approver=$users[0];
    		}else{
    			return('审核审批人错误!');
    		}
    		$model->from_date=$post['from_date'];
    		$model->to_date=$post['to_date'];
    		$model->vacate_type=$post['type'];
    		$model->msg=$post['msg'];
    		$model->create_at=date('Y-m-d H:i:s');
    		//保存模型数据
    		if($model->save()){
    			// 发送消息给审批人
    			$url=\yii\helpers\Url::toRoute(['deal','id'=>$model->id,'leader'=>'approver']);
    			$this->responseMsg($model->approver,$post['submitterName'].'的请假申请，请审核！',$url);
    			// 渲染页面
    			return $this->renderPartial('msg',['msg'=>'请假申请成功！']);
    		}else{
    			return('请假数据保存失败，请联系管理员！');
    		}
    		
    	}else{
            $userid = isset($_COOKIE["userid"])? $_COOKIE["userid"] :'0';
            if($userid=='0'){
                $code=$this->getCode();
                $userid=$this->getUserid($code);
            }
    		$user=user::find()->where(['userid'=>$userid])->One();
            //如果用户模型为空，则返回错误内容。
            if(empty($user)){
                return '此用户可能被删除掉！';
            }
            if($user->leader==10){
                //如果是职工
                if($user->department==1){
                    $where=['or',['and',['department'=>$user->department],['leader'=>'5']],['and',['leader'=>'0'],['like','department',$user->department.';']],['and',['leader'=>'0'],['department'=>$user->department]]];
                }else{
                    $where=['or',['and',['department'=>$user->department],['leader'=>'5']],['and',['leader'=>'0'],['like','department',';'.$user->department.';']],['and',['leader'=>'0'],['department'=>$user->department]]];
  
                }
            }elseif ($user->leader==5) {
                //如果是部门领导
                $where=['or',['and',['leader'=>0],['like','department',';'.$user->department]],['admin'=>2]];
            }elseif($userid==0){
                //如果是单位领导
                if($user->admin<2){
                    $where=['admin'=>2];
                }else{
                    $where=['admin'=>1];
                }
            }
            $models=user::find()->where($where)->asArray()->orderBy(['admin'=>SORT_DESC,'leader'=>SORT_ASC])->all();
            return $this->renderPartial('index',['models'=>$models,'user'=>$user]);
    	}
    	
    }
    /**
     * 处理请假流程
     * @param  [type] $id     vacate 表的主键id
     * @param  string $leader 表示审批和审核的权限标识，便于流程处理
     * @return [type]         [description]
     */
    public function actionDeal($id,$leader='approver'){
        // 通过cookie，限定必须在微信的内置浏览器中打开
        $userid = isset($_COOKIE["userid"])? $_COOKIE["userid"] :'0';
        //如果没有cookie，就回调获取code，然后换取userid
        if($userid=='0'){
            $code=$this->getCode();
            $userid=$this->getUserid($code);
        }
        // 流程处理
    	$vacate=Vacate::findOne($id);
        if($vacate->submitter!=$userid and $vacate->approver != $userid and $vacate->reviewer != $userid){
            return '用户参数错误！';
        }
    	$user=user::find()->select(['name','corpid'])->where(['userid'=>$vacate->submitter])->asArray()->One();
    	$name=$user['name'];
    	// 如果有post提交数据，则提交处理，否则直接渲染页面
    	if(Yii::$app->request->isPost){
    		// 如果不同意，则给申请人发送消息，审批没通过
    		if(Yii::$app->request->post('agree')=='0'){
    			//不同意，通知申请人，没有通过
    			$url=\yii\helpers\Url::toRoute(['list','userid'=>$vacate->submitter]);
    			$this->responseMsg($vacate->submitter,'您的请假，没有通过审批！',$url);
    		}elseif(Yii::$app->request->post('agree')=='1'){
    			// 否则，保存同意结果
    			if($leader=='approver'){
    				$vacate->approved=1;
    				$vacate->save();
    				if($vacate->reviewer =='0'){
    					// 通知申请人申请成功
    					$url=\yii\helpers\Url::toRoute(['list','userid'=>$vacate->submitter,'leader'=>'approver']);
    					$this->responseMsg($vacate->submitter,'您的请假，已经通过审批！',$url);
    					$this->dealAttendance($vacate->submitter,$vacate->from_date,$vacate->to_date,$vacate->vacate_type,$user['corpid'],$vacate->dayOrHalf);
    				}else{
    					// 给审批人发送审批通知
    					$url=\yii\helpers\Url::toRoute(['deal','id'=>$vacate->id,'leader'=>'reviewer']);
    					$this->responseMsg($vacate->reviewer,$name.'的请假申请，请审批！',$url);
    				}
    			}elseif ($leader=='reviewer') {
	    			// 保存同意结果
                    if($vacate->approved=='0'){
                        return $this->renderPartial('msg',['msg'=>'审核人又修改请假为不同意请假！']);
                    }
	    			$vacate->reviewed=1;
    				$vacate->save();
		    		// 通知申请人申请成功
					$url=\yii\helpers\Url::toRoute(['list','userid'=>$vacate->submitter]);
					$this->responseMsg($vacate->submitter,'您的请假，已经通过审批！',$url);
					$this->dealAttendance($vacate->submitter,$vacate->from_date,$vacate->to_date,$vacate->vacate_type,$user['corpid'],$vacate->dayOrHalf);
    			}
    		}else{
                return '非法参数！';
            }
    		// 渲染操作成功页面
			return $this->renderPartial('msg');
    	}else{
    		// 渲染页面
	    	return $this->renderPartial('deal',['vacate'=>$vacate,'name'=>$name]);
    	}
    	
    }
    /**
    *自己的请假列表
    */
    public function actionList(){
    	$userid = isset($_COOKIE["userid"])? $_COOKIE["userid"] :'0';
        //如果没有cookie，就回调获取code，然后换取userid
        if($userid=='0'){
            $code=$this->getCode();
            $userid=$this->getUserid($code);
        }
    	$vacate=Vacate::find()->where(['submitter'=>$userid])->orderBy('id desc')->limit(10)->asArray()->all();
    	$where=['in','userid'];
    	$arr=[$userid];
    	foreach ($vacate as $v) {
    		$arr[]=$v['approver'];
    		if($v['reviewer'] !='0'){
    			$arr[]=$v['reviewer'];
    		}
    	}
    	$where[]=array_unique($arr);
        $users=user::find()->select(['userid','name','leader'])->where($where)->asArray()->all();
    	$name=[];
        foreach ($users as $user) {
            $name[$user['userid']]['name']=$user['name'];
            $name[$user['userid']]['leader']=$user['leader'];
        }
    	return $this->renderPartial('list',['vacate'=>$vacate,'name'=>$name,'userid'=>$userid]);
    }
    /**
    *领导的审核列表
    */
    public function actionCheckList(){
        $userid=Yii::$app->request->get('userid','0');
        $filter=Yii::$app->request->get('filter','0');
        if($userid=='0'){
            return '此链接不合法！';
        }
        //显示近7天的申请
        $date=date('Y-m-d',strtotime('-7 days'));
        if($filter=='0'){//筛选未审批审核
            $where=['or',['and',['approver'=>$userid],['approved'=>0],['>','create_at',$date]],['and',['reviewer'=>$userid],['reviewed'=>0],['approved'=>1],['>','create_at',$date]]];
        }else{//所有的审批审核
            $where=['or',['and',['approver'=>$userid],['>','create_at',$date]],['and',['reviewer'=>$userid],['approved'=>1],['>','create_at',$date]]];
        }
        $vacate=Vacate::find()->where($where)->orderBy('id desc')->limit(30)->asArray()->all();
        $where=['in','userid'];
        $arr=[$userid];
        foreach ($vacate as $v) {
            $arr[]=$v['submitter'];
        }
        $where[]=array_unique($arr);
        $users=(new \yii\db\Query())
            ->select(['name'])
            ->from('wxe_department_user')
            ->where($where)
            ->indexBy('userid')
            ->column();
        return $this->renderPartial('checklist',['vacate'=>$vacate,'users'=>$users,'userid'=>$userid,'filter'=>$filter]);

    }
    /**
     * 操作成功的界面
     * @return [type] [description]
     */
    public function actionMsg(){
        $msg=Yii::$app->request->get('msg','操作成功！');
    	return $this->renderPartial('msg',['msg'=>$msg]);
    }
    /**
     * 请假成功，修改考勤表的记录
     * @param  [type] $userid    请假申请人的userid
     * @param   $dayOrHalf       0=全天 1=上午  2=下午
     * @param  [type] $from_date 开始日期
     * @param  [type] $to_date   结束日期
     * @param  [type] $type      请假类型 [0=>'迟到早退',1=>'全勤',2=>'旷工',3=>'事假',4=>'病假',5=>'公休',6=>'出差',7=>'外出',8=>'其它']
     * @return [type]            [description]
     */
    private function dealAttendance($userid,$from_date,$to_date,$type=3,$corpid,$dayOrHalf=0){
    	$user=user::find()->select('mobile')->where(['userid'=>$userid])->asArray()->One();
        if(empty($user)){
            return false;
        }
        //如果请全天假期，则循环插入上午和下午的请假，否则循环插入上午或者下午的考勤
    	for($i=strtotime($from_date);$i<=strtotime($to_date);$i+=86400){
    		$workday=workday::find()->where(['date'=>date('Y-m-d',$i)])->one();
    		if($workday->is_work_day==1){
                if($dayOrHalf=='0'){
                    $this->attendance($user['mobile'],$workday->date,1,$type);
                    $this->attendance($user['mobile'],$workday->date,2,$type);
                }elseif($dayOrHalf==1 or $dayOrHalf==2){
                    $this->attendance($user['mobile'],$workday->date,$dayOrHalf,$type);
                } 
    		}
    	}
        return true;
    }
    // public function actionTest($userid){
    //     $this->dealAttendance($userid,'2017-8-1','2017-8-4',7,'111',0);
    // }
    /**
     * 处理请假的数据
     * @return [type] [description]
     */
    private function attendance($mobile,$date,$dayOrHalf,$type){
        $attendance=NewAttendance::find()->where(['and',['mobile'=>$mobile],['work_date'=>$date],['am_pm'=>$dayOrHalf]])->one();
        if($attendance){
            $attendance->attendance=$type;
            $attendance->am_pm=$dayOrHalf;
            $attendance->num=0;
        }else{
            $attendance=new NewAttendance();
            $attendance->mobile=$mobile;
            $attendance->work_date=$date;
            $attendance->attendance=$type;
            $attendance->am_pm=$dayOrHalf;
        }
        $attendance->save();
    }
    /**
     * 获取code值
     * @return [type] [description]
     */
    private function getCode(){
    	$code=Yii::$app->request->get('code');
    	if(empty($code)){
    		$callback='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    		$options=['appid'=>"ww1de94f786c71be9f","appsecret"=>"3A7MMACbNQrBVKNSjWwXvl4rI1KDSd48fYtwwo2Xzrc","agentid"=>'1000002'];
			$qy=new QyWechat($options);
    		$url=$qy->getOauthRedirect($callback);
    		header("location: $url");
    	}else{
    		return $code;
    	}
    }
    private function getUserid($code){
    	$options=['appid'=>"ww1de94f786c71be9f","appsecret"=>"3A7MMACbNQrBVKNSjWwXvl4rI1KDSd48fYtwwo2Xzrc","agentid"=>'1000002'];
		$qy=new QyWechat($options);
		$result=$qy->getUserId($code,'1000002');
		//如果获取不到userid，则终止程序，并报错误提示。
		if(!isset( $result['UserId']) or $result['UserId']=='')die('no userid exist!');
        //设置cookie
        setcookie("userid",$result['UserId'], time()+86400);
		return $result['UserId'];
    }
    private function responseMsg($userid,$title,$url){
    	$options=['appid'=>"ww1de94f786c71be9f","appsecret"=>"3A7MMACbNQrBVKNSjWwXvl4rI1KDSd48fYtwwo2Xzrc","agentid"=>'1000002'];
		$qy=new QyWechat($options);
		$date=array(
            "touser" => $userid,
            "toparty" => "",
            "totag" => "",
            "agentid" => 1000002,    //应用id
            "msgtype" => "news",  //根据信息类型，选择下面对应的信息结构体
            "news" => array(            //不支持保密
                         "articles" => array(    //articles  图文消息，一个图文消息支持1到10个图文
                             array(
                                 "title" => $title,  //标题
                                 "description" => date('Y-m-d H:i:s').' '.$title, //描述
                                 "url" => 'http://'.$_SERVER['HTTP_HOST'].$url, //点击后跳转的链接。
                                 "picurl" =>"",  //图文消息的图片链接,支持JPG、PNG格式，较好的效果为大图640*320，小图80*80。如不填，在客户端不显示图片
                              ),
                        ),
                    ),
            );
		$result=$qy->sendMessage($date);
    }
}
