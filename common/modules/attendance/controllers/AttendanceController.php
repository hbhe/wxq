<?php

namespace common\modules\attendance\controllers;
use Yii;
use common\modules\attendance\models\attendance;
use common\modules\attendance\models\AttendanceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttendanceController implements the CRUD actions for attendance model.
 */
class AttendanceController extends Controller
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
     * Lists all attendance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttendanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single attendance model.
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
     * Creates a new attendance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new attendance();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing attendance model.
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
     * Deletes an existing attendance model.
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
     * 月考勤信息汇总
     * @return mixed
     */
    public function actionSummary(){
        $date=Yii::$app->request->post('to');//提交日期，如果没有就默认当期日
        $beginDate=Yii::$app->request->post('from');//获取提交日当月的第一天
        if(empty($date)){
            $date=date('Y-m-d',strtotime('-1 day'));
        }
        if(empty($beginDate)){
            $beginDate=date('Y-m-1');
        }
        $corpid=Yii::$app->session->get('corpid');
        $query=(new \yii\db\Query())
            ->select('u.name,u.department,sum(case when a.attendance=0 then 1 else 0 end) as chidao,count(case when (a.attendance=1 and a.kuanggong=0) then 1 end) as quanqin,sum(a.remarks) as bushiquanqin,sum(a.kuanggong) as kuanggong,count(case when a.attendance=3 then 1 end) as qingjia,count(case when a.attendance=4 then 1 end) as xiujia')
            ->from('wxe_attendance as a')
            ->leftJoin('wxe_department_user as u','u.mobile=a.mobile')
            ->where(['and',['a.corpid'=>$corpid],['between','create_at',$beginDate,$date]])
            ->groupBy('a.mobile')
            ->orderBy('u.department');

        $models=$query->all();
        //获取查询月的工作日期天数
        $count=(new \yii\db\Query())
            ->select('id')
            ->from('wxe_workday')
            ->where(['and',['is_work_day'=>1],['between','date',$beginDate,$date]])
            ->count();
        return $this->render('summary', [
            'models' => $models,
            'count'=>$count,
            'date'=>$date,
            'beginDate'=>$beginDate,
        ]);
    }
    /**
     * Finds the attendance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return attendance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = attendance::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
