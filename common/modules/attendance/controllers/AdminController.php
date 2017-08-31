<?php

namespace common\modules\attendance\controllers;

use Yii;
use common\modules\attendance\models\admin;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for admin model.
 */
class AdminController extends Controller
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
     * Lists all admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->session->get('auth')=='admin'){
            $where=['corpid'=>Yii::$app->session->get('corpid')];
        }else{
            $where=['corpid'=>Yii::$app->session->get('corpid'),'auth'=>Yii::$app->session->get('auth')];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => admin::find()->where($where),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single admin model.
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
     * Creates a new admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new admin();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         $model->corpid=Yii::$app->session->get('corpid');
    //         $model->secret=Yii::$app->session->get('secret');
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Updates an existing admin model.
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
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing admin model.
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
     * Finds the admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = admin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * 登录
     * 设置session（包含corpid secret）
    */
    public function actionLogin(){
        $request=Yii::$app->request;
        if($request->isPost){
            $post=Yii::$app->request->post('admin');
            $model=admin::find()->where(['admin'=>$post['admin'],'password'=>$post['password']])->one();
            if($model!==null){
                $session = Yii::$app->session;
                //设置session信息
                $session->set('corpid',$model->corpid);
                $session->set('secret',$model->secret);
                $session->set('auth',$model->auth);
                //更新时间和ip
                $model->login_at=date('Y-m-d H:i:s');
                $model->login_ip=$request->userIp;
                $model->save();
                return $this->redirect(['index']);
            }else{
                Yii::$app->session->setFlash('alert',['body'=>'用户名或者密码错误！','options'=>['class' => 'alert-warning']]);
                return $this->redirect(['login']);
            }
        }
        $model = new Admin();
        return $this->renderPartial('login', ['model' => $model]);
    }
    /**
     * [actionLogout 退出]
     * @return [type] [description]
     */
        public function actionLogout(){
        $session=Yii::$app->session;
        $session->remove('corpid');
        $session->remove('secret');
        $session->remove('auth');
        return $this->redirect(['login']);
    }

}
