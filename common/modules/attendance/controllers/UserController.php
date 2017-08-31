<?php

namespace common\modules\attendance\controllers;

use Yii;
use common\modules\attendance\models\user;
use common\modules\attendance\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use  wechat\models\QyWechat;
use common\modules\attendance\models\configuration;
use common\modules\attendance\models\workday;
/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends Controller
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
     * Lists all user models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->session->get('corpid')===null){
            return $this->redirect(['admin/login']);
        }
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // 判断目前是否是上班时间
        $is_work='休息时间';
        $workday=workday::find()->select('is_work_day')->where(['date'=>date('Y-m-d')])->asArray()->one();
        if($workday['is_work_day']==1){
            $config=configuration::getConfiguration(1,Yii::$app->session->get('corpid'));
            $m_start=strtotime(date('Y-m-d').$config['attendance_start_time']);
            $m_end=strtotime(date('Y-m-d').$config['attendance_rest_start_time']);
            $a_start=strtotime(date('Y-m-d').$config['attendance_rest_end_time']);
            $a_end=strtotime(date('Y-m-d').$config['attendance_end_time']);
            if(($m_start<= time() and time() <= $m_end ) or ($a_start<= time() and time() <=$a_end)){
                $is_work="上班时间";
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'is_work'=>$is_work,
        ]);
    }

    /**
     * Displays a single user model.
     * @param integer $id
     * @return mixed
     */
    // public function actionView($id)
    // {
    //     return $this->render('view', [
    //         'model' => $this->findModel($id),
    //     ]);
    // }

    /**
     * Creates a new user model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new user();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Updates an existing user model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing user model.
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
     * Finds the user model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return user the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = user::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * 同步员工列表到数据库
     */
    public function actionSync(){
        // header("Content-type: text/html; charset=utf-8"); 
        $appid=$options['appid']=Yii::$app->session->get('corpid');
        $options['appsecret']=Yii::$app->session->get('secret');
        //从企业号获取员工列表
        $result=(new QyWechat($options))->getUserListInfo(1,1,0);
        $userlist=$result['userlist'];
        //先删除数据库中所有的部门,再将同步的员工列表批量插入
        if($userlist && is_array($userlist)){
            user::deleteAll("corpid='{$appid}'");
            $new_arr=array();
            foreach ($userlist as $v) {
                if($v['position']=='退休'){
                    continue;
                }
                $data['userid']= $v['userid'];
                $data['name']=$v['name'];
                if(count($v['department'])==1){
                    $data['department']=$v['department'][0];
                }else{
                    $data['department']=implode(';',$v['department']).';';
                }
                $data['position']=isset($v['position'])? $v['position'] : '';
                $data['mobile']=isset($v['mobile']) ? $v['mobile'] : '';
                $data['gender']=isset($v['gender']) ? $v['gender'] : 0;
                $data['email']=isset($v['email']) ? $v['email'] :'';
                // $data['weixinid']=isset($v['weixinid']) ? $v['weixinid'] : '' ;
                $data['avatar']=isset($v['avatar']) ? $v['avatar'] : '';
                $data['status']=isset($v['status']) ? $v['status'] : 4;
                // $data['extattr']=isset($v['extattr']) ? $v['extattr'] : '';
                // 通过职务名称自动给人员赋予权限制
                if($v['position']=='部门领导'){
                    $data['leader']=5;
                }elseif($v['position']=='单位领导'){
                    $data['leader']=0;
                }else{
                    $data['leader']=10;
                }
                $data['corpid']=$appid;
                $data['update_at']=date('Y-m-d H:m:s');
                $new_arr[]=$data;
            }

            //如果数组不为空则批量插入
            if(!empty($new_arr)){
                Yii::$app->db->createCommand()
                ->batchInsert(user::tableName(),['userid','name','department','position','mobile','gender','email','avatar','status','leader','corpid','update_at'],$new_arr)
                ->execute();
            }
            return $this->redirect(['index','appid'=>$appid]);
        }
    }
}
