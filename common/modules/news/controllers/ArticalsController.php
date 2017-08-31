<?php

namespace common\modules\news\controllers;
use Yii;
use common\modules\news\models\Articals;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use  wechat\models\QyWechat;
/**
 * ArticalsController implements the CRUD actions for Articals model.
 */
class ArticalsController extends Controller
{
    public $layout='@common/modules/attendance/views/layouts/main';
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
     * Lists all Articals models.
     * @return mixed
     */
    public function actionIndex()
    {
        $corpid=Yii::$app->session->get('corpid');
        if(Yii::$app->session->get('auth')=='xinwen'){
            $where=['corpid'=>$corpid,'banner_id'=>1];
            $name='新闻';
        }elseif (Yii::$app->session->get('auth')=='gonggao') {
            $where=['corpid'=>$corpid,'banner_id'=>2];
            $name='公告';
        }else{
            $where=['corpid'=>$corpid];
            $name='新闻公告';
        }
        $dataProvider = new ActiveDataProvider([
            'query' => Articals::find()->select(['id','banner_id','author','title','issue','create_at'])->where($where),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'name'=>$name
        ]);
    }

    /**
     * Displays a single Articals model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Articals model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Articals();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //跳转到列表页
            return $this->redirect(['index']);
        } else {
            $model->create_at=date('Y-m-d H:i:s');
            $model->order=0;
            $model->corpid=Yii::$app->session->get('corpid');
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Articals model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_issue=$model->issue;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //如果新闻公告由审核变为发布则发布图文
            if($old_issue==0 && $model->issue==1){
                $this->sendNews($model);//发送图文信息到相应的应用id
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Articals model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Articals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Articals the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Articals::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * 往企业号的应用中发送图文消息
     * @param  [type] $model [description]
     * @return [type]        [description]
     */
    private function sendNews($model){
        //从gh表中获取应用的id
        if($model->banner_id==1){
            $where=['appId'=>$model->corpid,'sid'=>'gswh'];
        }else{
            $where=['appId'=>$model->corpid,'sid'=>'zwgk'];
        }
        $gh=(new \yii\db\Query())
            ->select('*')
            ->from('gh')
            ->where($where)
            ->one();
        $date=array(
            "touser" => "@all",
            "toparty" => "PartyID1|PartyID2 ",
            "totag" => "TagID1|TagID2 ",
            "safe"=>"0",            //是否为保密消息，对于news无效
            "agentid" => $gh['gh_id'],    //应用id
            "msgtype" => "news",  //根据信息类型，选择下面对应的信息结构体
            "news" => array(            //不支持保密
                         "articles" => array(    //articles  图文消息，一个图文消息支持1到10个图文
                             array(
                                 "title" => $model->title,             //标题
                                 "description" => strip_tags(substr($model->artical,0,100)), //描述
                                 "url" => "http://gs-admin.buy027.com/index.php?r=news%2Fdefault%2Fview&id={$model->id}",                 //点击后跳转的链接。可根据url里面带的code参数校验员工的真实身份。
                                 "picurl" => empty($model->img) ? '' : 'http://'.$_SERVER['HTTP_HOST'].(\Yii::$app->imagemanager->getImagePath($model->img, 9999, 9999)),         //图文消息的图片链接,支持JPG、PNG格式，较好的效果为大图640*320，
                                                                 //小图80*80。如不填，在客户端不显示图片
                              ),
                        ),
                    ),
            );
        $options['appid']=$gh['appId'];
        $options['appsecret']=$gh['appSecret'];
        $options['agentid']=$gh['gh_id'];
        //往企业号中发送新闻消息
        $result=(new QyWechat($options))->sendMessage($date);
    }
}
