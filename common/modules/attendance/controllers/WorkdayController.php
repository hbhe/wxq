<?php

namespace common\modules\attendance\controllers;

use Yii;
use common\modules\attendance\models\workday;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WorkdayController implements the CRUD actions for workday model.
 */
class WorkdayController extends Controller
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
     * Lists all workday models.
     * @return mixed
     */
    public function actionIndex()
    {
        $corpid=Yii::$app->session->get('corpid');
        $dataProvider = new ActiveDataProvider([
            'query' => workday::find()->where(['and',['=','corpid',$corpid],['>=','date',date('Y-m-d')]]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single workday model.
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
     * Creates a new workday model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $workday=array();
        $request=Yii::$app->request;   
    /*如果是post数据则先查询数据是否存在，如果存则跳转到首页，不存在则生成即日起至年末的工作日*/
        if($request->isPost){
            // 获取提交的年份，如果为空则默认为今年
            $year=$request->post('date',date('Y'));
            $result=workday::find()->where(['like','date',"{$year}%",false])->limit(1)->all();
            //如果存在则跳转到index页，否则生成请求的工作日，存储在$workday数组中
            if($result){
                Yii::$app->session->setFlash('alert',['body'=>'您要添加的工作日已经存在！','options'=>['class' => 'alert-info']]);
                return $this->redirect(['index']);
                exit(0);
            }else{
                $lastyear=$year+1;//明年
                $day=date('Y-m-d',time());//代表即刻起到年末的日子
                while(date('Y',strtotime($day))<$lastyear){
                    //判断 $day 是否是周末
                    $w=date('w',strtotime($day));
                    $corpid=Yii::$app->session->get('corpid');
                    if($w==0 or $w==6){
                        //周末
                        $workday[]=array('date'=>$day,'is_work_day'=>0,'corpid'=>$corpid);
                    }else{
                        //工作日
                        $workday[]=array('date'=>$day,'is_work_day'=>1,'corpid'=>$corpid);
                    }
                    
                    //日期加1天
                    $day=date('Y-m-d',strtotime("$day +1 day"));
                }
            }
            Yii::$app->db->createCommand()->batchInsert(workday::tableName(),['date','is_work_day','corpid'],$workday)->execute();
            return $this->redirect(['index']);
        }else {
            $model=new workday();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing workday model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $page=isset($_COOKIE['page']) ? $_COOKIE['page'] :1;
            return $this->redirect(['index','page'=>$page]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing workday model.
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
     * Finds the workday model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return workday the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = workday::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
