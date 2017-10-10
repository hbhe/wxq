<?php
namespace common\modules\attendance\controllers;

use Yii;
use common\modules\attendance\models\Meeting;
use common\modules\attendance\models\Participant;
use common\modules\attendance\models\AddressBook;
use common\modules\attendance\models\user;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use wechat\models\QyWechat;
use wechat\models\Wechat;
/**
 * MeetingController implements the CRUD actions for Meeting model.
 */
class MeetingController extends Controller
{
    public $layout='main';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Meeting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Meeting::find(),
            'sort' => [
            'defaultOrder' => [
                'id' => SORT_DESC,            
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Meeting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $participant=$this->getParticipant($id);
        return $this->render('view', [
            'model' => $this->findModel($id),'participant'=>$participant
        ]);
    }

    /**
     * Creates a new Meeting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Meeting();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //将参会人员保存到paricipant表中
            $users=$_POST['users'];
            $users=array_unique($users);
            // 循环插入
            $Participant = new Participant();
            foreach ($users as $user) {
              $Participant->isNewRecord = true;
              $Participant->meeting_id=$model->id;
              $Participant->userid=$user;
              $Participant->status_time=date("Y-m-d H:i:s");
              $Participant->save() && $Participant->id=0;
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            //获得通讯录
            $AddressBook=AddressBook::book(Yii::$app->session->get('corpid'),'all');
            //获得部门领导和单位领导的名字，作为会议发起人
            $authors=$this->getAuthors();
            return $this->render('create', [
                'model' => $model,'AddressBook'=>$AddressBook,'authors'=>$authors
            ]);
        }
    }

    /**
     * Updates an existing Meeting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $authors=$this->getAuthors();
            return $this->render('update', [
                'model' => $model,'authors'=>$authors
            ]);
        }
    }

    /**
     * Deletes an existing Meeting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        $Participant=Participant::deleteAll(['meeting_id'=>$id]);

        return $this->redirect(['index']);
    }
    /**
     * 发布会议信息给参会人员
     * @param  [type] $id [会议id]
     * @return [type]     [description]
     */
    public function actionPublish($id){

        $users=Participant::find()->select(['userid','status'])->where(['meeting_id'=>$id])->asArray()->all();
        $str='';
        foreach ($users as $key => $user) {
            $str.=$user['userid'].'|';
            if($user['status'] !=0){
                Yii::$app->session->setFlash('alert',['body'=>'本会议已经发布过!','options'=>['class' => 'alert-danger']]);
                return $this->redirect(['view','id'=>$id]);
            }
        }
        $str=trim($str,'|');
        $url=\yii\helpers\Url::toRoute(['confirm','id'=>$id]);
        $result=$this->responseMsg($str,'会议通知：',$url);
        if(!$result){
            Yii::$app->session->setFlash('alert',['body'=>'发布失败，请联系技术人员!','options'=>['class' => 'alert-danger']]);
        }else{
            Yii::$app->session->setFlash('alert',['body'=>'会议发布成功!','options'=>['class' => 'alert-info']]);
            setcookie("meeting_id".$id,1, time()+86400);//设置cookie，防止重复刷新发布
            Participant::updateAll(['status'=>0],'meeting_id='.$id);//同一个会议，重新编辑后再次发布，先清除掉参会人员的状态
        }

        return $this->redirect(['view','id'=>$id]);
    }
    /**
     * 参会人员确认处理，以及显示状态
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionConfirm($id){
        $userid = isset($_COOKIE["userid"])? $_COOKIE["userid"] :'0';
        if($userid=='0'){
            $code=$this->getCode();
            $userid=$this->getUserid($code);
        }
        $meeting=Meeting::find()->where(['id'=>$id])->one();
        $participant=$this->getParticipant($id);
        if(empty($meeting) or empty($participant)){
            return $this->renderPartial('/default/msg',['msg'=>'此会议已经被删除掉！']);
        }
        return $this->renderPartial('confirm',['userid'=>$userid,'meeting'=>$meeting,'participant'=>$participant]);
    }
    /**
     * 处理会议确认和请假、扫码签到的操作，然后如果成功，则转入成功的界面
     * @return [type] [description]
     */
    public function actionDeal(){
        // 获取userid
        $userid = isset($_COOKIE["userid"])? $_COOKIE["userid"] :'0';
        if($userid=='0'){
            $code=$this->getCode();
            $userid=$this->getUserid($code);
        }
        // 获取参数
        $request=Yii::$app->request;
        $meeting_id=$request->get('meeting_id',0);
        $status=$request->get('status',0);
        //过滤状态值，如果状态值在范围内，则进行处理操作
        if($status==1 || $status==2 || $status==3){
            $participant=Participant::find()->where(['meeting_id'=>$meeting_id,'userid'=>$userid])->one();
            if($participant==null){
                $msg='您不是邀请的参会人员！';
                return $this->renderPartial('/default/msg',['msg'=>$msg]);
            }
            if($participant->status==3){
                $msg='您已经扫码通过了！';
            }else{
                // 修改参会状态和时间
                $participant->status=$status;
                $participant->status_time=date('Y-m-d H:i:s');
                // 设置处理信息
                if($participant->save()){
                    if($status==1){
                        $msg='会议确认成功！';
                    }elseif($status==2){
                        $msg='已经向会议发起者请假，请等待批准！';
                    }elseif($status==3){
                        $msg='扫码签到成功！';
                    }
                }else{
                    $msg='处理数据失败！';
                }
            }
        }else{//状态值报错
            $msg='参数错误，请联系管理员！';
        }
        return $this->renderPartial('/default/msg',['msg'=>$msg]);
    }
    /**
    *填写请假理由
    * @parm userid 申请人userid,userid,
    * @parm meeting_author 会议发起人
    * @parm msg 请假理由
    */
    public function actionVacate(){
        // 获取get提交的参数
        $request=Yii::$app->request;
        $userid=$request->get('userid','0');
        $meeting_id=$request->get('meeting_id','0');
        $meeting_author=$request->get('meeting_author','0');
        // 如果参数错误则到错误页报错
        if($userid=='0' or $meeting_author=='0' or $meeting_id=='0'){
            return $this->renderPartial('/default/msg',['msg'=>'参数不完整！']);
        }
        // 如果有post提交的数据，则处理
        if($request->isPost){
            $msg=$request->post('msg','0');
            if($msg=='0'){
                return $this->renderPartial('/default/msg',['msg'=>'请假理由为空！']);
            }
            $user=user::find()->select('name')->where(['userid'=>$userid])->asArray()->one();
            $name=$user['name'];
            $url=\yii\helpers\Url::toRoute(['approve','meeting_id'=>$meeting_id,'name'=>$name,'userid'=>$userid,'msg'=>$msg]);
            $this->responseMsg($meeting_author,$name.'的会议请假申请',$url);
            return $this->renderPartial('/default/msg',['msg'=>"请假申请提交成功！"]);
        }
        // 直接渲染页面
        return $this->renderPartial('vacate',['userid'=>$userid,'meeting_author'=>$meeting_author,'meeting_id'=>$meeting_id]);
    }
    /**
    * 审批会议请假页面
    * @parm userid 申请人userid
    * @parm name 请假人名称
    * @parm msg 请假理由
    */
    public function actionApprove(){
        // 获取get提交的参数
        $request=Yii::$app->request;
        $name=$request->get('name','0');
        $userid=$request->get('userid','0');
        $meeting_id=$request->get('meeting_id',0);
        $msg=$request->get('msg','0');
        // 判断参数的完整性
        if($name=='0' or $userid=='0' or $msg=='0'){
            return $this->renderPartial('/default/msg',['msg'=>'传递的参数不完整！']);
        }
        $meet=Meeting::find()->where(['id'=>$meeting_id])->asArray()->one();
        // 如果有get提交的是判断后的数据，则跳转到相应的页面逻辑
        $agreen=$request->get('agreen','0');
        if($agreen=='yes'){
            //通知用户请假批准，并修改用户的会议状态为请假
            $url=\yii\helpers\Url::toRoute(['/attendance/default/msg','msg'=>'会议请假成功！']);
            $this->responseMsg($userid,'您的会议请假通过！',$url);
            $participant=Participant::find()->where(['meeting_id'=>$meeting_id,'userid'=>$userid])->one();
            $participant->status=2;
            $participant->status_time=date('Y-m-d H:i:s');
            $participant->save();
            return $this->renderPartial('/default/msg',['msg'=>"{$name}的会议请假s申请成功。"]);
        }elseif($agreen=='no'){
            // 发通知给用户，请假失败，修改用户的会议状态为确认
            $url=\yii\helpers\Url::toRoute(['/attendance/default/msg','msg'=>'会议请假没有通过！']);
            $this->responseMsg($userid,'您的会议请假没有通过！',$url);
            $participant=Participant::find()->where(['meeting_id'=>$meeting_id,'userid'=>$userid])->one();
            $participant->status=1;
            $participant->status_time=date('Y-m-d H:i:s');
            $participant->save();
            return $this->renderPartial('/default/msg',['msg'=>"不同意{$name}的会议请假。"]);
        }
        return $this->renderPartial('approve',['userid'=>$userid,'name'=>$name,'msg'=>$msg,'meet'=>$meet]);
    }
    /**
     * Finds the Meeting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Meeting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Meeting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
    *获取部门领导和单位领导的名字
    * return array  [userid=>name]
    */
    private function getAuthors(){
        $authors=(new \yii\db\Query()) 
            ->select('name') 
            ->from('wxe_department_user')
            ->where(['<','leader',10])
            ->orderBy('userid')
            ->indexBy('userid')
            ->column();
        return $authors;
    }
    /**
     * 获取参会人员的状态
     * @return [type] 参会人员数组
     */
    private function getParticipant($id){
        // 查找参会人员
        $users=(new \yii\db\Query())
                ->select(['d.name','p.status'])
                ->from('wxe_participant as p')
                ->leftJoin('wxe_department_user as d','p.userid= d.userid')
                ->where(['p.meeting_id'=>$id])
                ->orderBy('p.status')
                ->all();
        //按状态分类 0=未收到通知的参会人员 1=确认收到通知的参会人员 2=请假的参会人员  3=签到的参会人员
        $participant=[];
        foreach ($users as $user) {
            $participant[$user['status']][]=$user['name'];
        }
        return $participant;
    }
    /**
     * 获取code值
     * @return [type] [description]
     */
    private function getCode(){
        $code=Yii::$app->request->get('code');
        if(empty($code)){
            $callback='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $options=['appid'=>"ww1de94f786c71be9f","appsecret"=>"JKOkb04Faan5R5W0xEgK8Lyka9NVPvu3dwhKq85bjjg","agentid"=>'1000003'];
            $qy=new QyWechat($options);
            $url=$qy->getOauthRedirect($callback);
            header("location: $url");
        }else{
            return $code;
        }
    }
    private function getUserid($code){
        $options=['appid'=>"ww1de94f786c71be9f","appsecret"=>"JKOkb04Faan5R5W0xEgK8Lyka9NVPvu3dwhKq85bjjg","agentid"=>'1000003'];
        $qy=new QyWechat($options);
        $result=$qy->getUserId($code,'1000003');
        //如果获取不到userid，则终止程序，并报错误提示。
        if(!isset( $result['UserId']) or $result['UserId']=='')die('no userid exist!');
        //设置cookie
        setcookie("userid",$result['UserId'], time()+86400);
        return $result['UserId'];
    }
    /**
     * 发送图文
     * @param  [type] $userid [description]
     * @param  [type] $title  [description]
     * @param  [type] $url    [description]
     * @return [type]         [description]
     */
    private function responseMsg($userid,$title,$url){
        $options=['appid'=>"ww1de94f786c71be9f","appsecret"=>"JKOkb04Faan5R5W0xEgK8Lyka9NVPvu3dwhKq85bjjg","agentid"=>'1000003'];
        $qy=new QyWechat($options);
        $date=array(
            "touser" => $userid,
            "toparty" => "",
            "totag" => "",
           // "safe"=>"0",            //是否为保密消息，对于news无效
            "agentid" => 1000003,    //应用id
            "msgtype" => "news",  //根据信息类型，选择下面对应的信息结构体
            "news" => array(            //不支持保密
                         "articles" => array(    //articles  图文消息，一个图文消息支持1到10个图文
                             array(
                                 "title" => $title,             //标题
                                 "description" => $title, //描述
                                 "url" => 'http://'.$_SERVER['HTTP_HOST'].$url,                 //点击后跳转的链接。可根据url里面带的code参数校验员工的真实身份。
                                 "picurl" =>"http://gs-admin.buy027.com/img/timg%20(1).png",         //图文消息的图片链接,支持JPG、PNG格式，较好的效果为大图640*320，
                                                                 //小图80*80。如不填，在客户端不显示图片
                              ),
                        ),
                    ),
            );
        $result=$qy->sendMessage($date);
        return $result;
    }
}
