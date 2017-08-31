<?php

namespace common\modules\attendance\controllers;

use Yii;
use common\modules\attendance\models\department;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use  wechat\models\QyWechat;
/**
 * DepartmentController implements the CRUD actions for department model.
 */
class DepartmentController extends Controller
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
     * Lists all department models.
     * @return mixed
     */
    public function actionIndex()
    {
        $corpid=Yii::$app->session->get('corpid');
        $dataProvider = new ActiveDataProvider([
            'query' => department::find()->where(['corpid'=>$corpid])->orderBy('path'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single department model.
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
     * Creates a new department model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new department();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Updates an existing department model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionUpdate($id)
    // {
    //     $model = $this->findModel($id);

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('update', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Deletes an existing department model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();

    //     return $this->redirect(['index']);
    // }

    /**
     * Finds the department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return department the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    // protected function findModel($id)
    // {
    //     if (($model = department::findOne($id)) !== null) {
    //         return $model;
    //     } else {
    //         throw new NotFoundHttpException('The requested page does not exist.');
    //     }
    // }
    /**
     * 同步部门列表到数据库
     */
    public function actionSync(){
        $appid=$options['appid']=Yii::$app->session->get('corpid','');
        $options['appsecret']=Yii::$app->session->get('secret');
        if($appid==''){die('连接超时，后退，刷新页面，请重新登录！');}
        // var_dump($options);
        //从企业号获取部门列表
        $result=(new QyWechat($options))->getDepartment();
        $department=$result['department'];
        //先删除数据库中所有的部门,再将同步的部门列表批量插入
        if($department && is_array($department)){
            department::deleteAll("corpid='{$appid}'");
            $new_arr=array();
            foreach ($department as $v) {
                $data=$v;
                $data['corpid']=$appid;
                //组成部门路径字符串
                if($v['parentid']==0){
                    $data['path']=$v['name'].',';
                }else{
                    $data['path']=$new_arr[$v['parentid']]['path'].$v['name'].',';
                }
                $new_arr[$v['id']]=$data;
            }
            //如果数组不为空则批量插入
            if(!empty($new_arr)){
                Yii::$app->db->createCommand()
                ->batchInsert(department::tableName(),['department_id','name','parentid','order','corpid','path'],$new_arr)
                ->execute();
            }
            return $this->redirect(['index','appid'=>$appid]);
        }
    }
}
