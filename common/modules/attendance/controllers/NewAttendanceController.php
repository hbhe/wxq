<?php

namespace common\modules\attendance\controllers;

use Yii;
use common\modules\attendance\models\NewAttendance;
use common\modules\attendance\models\NewAttendanceSearch;
use common\modules\attendance\models\user;
use common\modules\attendance\models\Vacate;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NewAttendanceController implements the CRUD actions for NewAttendance model.
 */
class NewAttendanceController extends Controller
{
    /**
     * @inheritdoc
     */
    public $layout='main';
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
     * Lists all NewAttendance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewAttendanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewAttendance model.
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
     * Creates a new NewAttendance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new NewAttendance();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Updates an existing NewAttendance model.
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
     * Deletes an existing NewAttendance model.
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
     * Finds the NewAttendance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NewAttendance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NewAttendance::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * 月考勤信息汇总
     * @return mixed
     */
    public function actionSummary(){
        $request=Yii::$app->request;
        $date=$request->post('to',date('Y-m-d',strtotime('-1 day')));//提交日期，如果没有就默认当期日
        $beginDate=$request->post('from',date('Y-m-1',strtotime($date)));//获取提交日当月的第一天
        $department=$request->post('department',0);
        // 获取用电话号码做键的数组
        $where=array();
        if($department=='1'){
            $where=['or',['department'=>'1'],['like','department','1;']];
        }elseif ($department>1) {
            $where=['department'=>$department];
        }
        $users_arr=user::find()->select(['name','userid','department','mobile'])->where($where)->orderBy('department')->asArray()->all();
        $users=array();
        $mobile_list=array();
        foreach ($users_arr as $u) {
            $mobile='m'.$u['mobile'];
            $users[$mobile]['name']=$u['name'];
            $users[$mobile]['userid']=$u['userid'];
            $users[$mobile]['department']=$u['department'];
            //如果查询某个部门的考勤，则筛选出相应的电话号码数组
            if($department){
                if($department==1){
                    if($u['department']==1 or strpos($u['department'], '1;')!==false){
                        $mobile_list[]=$u['mobile'];
                    }
                }else{
                    if($department==$u['department']){
                        $mobile_list[]=$u['mobile'];
                    }
                }
            }
        }
        //获取考勤信息数组
        $where=['between','work_date',$beginDate,$date];
        if($department){
            $where=['and',['between','work_date',$beginDate,$date],['in','mobile',$mobile_list]];
        }
        $attendance=(new \yii\db\Query())
            ->select('mobile,sum(case when (attendance=1 and start_at is not null and end_at is not null) then 1 else 0 end) as quanqin,sum(case when (attendance=0 or(attendance=1 and (start_at is null or end_at is null) )) then 1 else 0 end)as chidao_am_pm,sum(case when attendance=0 then num else 0 end) as chidao,count(case when (attendance=1 and (start_at is null or end_at is null)) then 1 end) as chidao2,count(case when attendance=3  then 1 end) as shijia,count(case when attendance=4  then 1 end) as bingjia,count(case when attendance=5  then 1 end) as gongxiu,count(case when attendance=6  then 1 end) as chuchai,count(case when attendance=7  then 1 end) as waichu,count(case when attendance=8  then 1 end) as qita')
            ->from('wxe_new_attendance')
            ->where($where)
            ->groupBy('mobile')
            ->all();
        
        //合并新的models数组，
        foreach ($attendance as $a) {
            if(isset($users['m'.$a['mobile']])){
               $users['m'.$a['mobile']]['a']=$a;
            }else{
                $users['m'.$a['mobile']]['name']='无';
                $users['m'.$a['mobile']]['userid']='wu';
                $users['m'.$a['mobile']]['department']=1;
                $users['m'.$a['mobile']]['a']=$a;
            }
            
        }
        //获取查询月的工作日期天数
        $count=(new \yii\db\Query())
            ->select('id')
            ->from('wxe_workday')
            ->where(['and',['is_work_day'=>1],['between','date',$beginDate,$date]])
            ->count();
        return $this->render('summary', [
            'users'=>$users,
            'count'=>$count,
            'date'=>$date,
            'beginDate'=>$beginDate,
            'department_id'=>$department,
        ]);
    }
    /**
    *查看请假具体信息
    */
    public function actionVacate(){
        $request=Yii::$app->request;
        //如果是get提交的就查询get的数据
        if($request->isGet){
            $from=$request->get('from',date('Y-m-1'));
            $to=$request->get('to',date('Y-m-d',strtotime('-1 day')));
            $userid=$request->get('userid',0);
            $name=$request->get('name','无');
            if($userid){
                $vacates=Vacate::find()->where(['and',['submitter'=>$userid],['between','create_at',$from,$to],['and',['approved'=>1],['or',['reviewed'=>1],['reviewer'=>0]]]])->orderBy('id desc')->asArray()->all();
            }else{
                $vacates=Vacate::find()->where(['between','create_at',$from,$to])->orderBy('id desc')->asArray()->all();
            }
        }
        //如果是post提交的就，查询post提交的数据
        if($request->isPost){
            $from=$request->post('from',date('Y-m-1'));
            $to=$request->post('to',date('Y-m-d',strtotime('-1 day')));
            $vacates=Vacate::find()->where(['between','create_at',$from,$to])->orderBy('id desc')->asArray()->all();
        }
        //获取姓名
        $users=(new \yii\db\Query()) 
            ->select('name') 
            ->from('wxe_department_user')   
            ->indexBy('userid')
            ->column();
        return $this->render('vacate',['users'=>$users,'vacates'=>$vacates,'from'=>$from,'to'=>$to]);
    }
    /**
    *查出异常或者不在单位的人员名单
    */
    public function actionException(){
        $search_time=time()-3600;
        $where=['or',['<','update_at',$search_time],['state'=>2]];
        $models=user::find()->where($where)->orderBy('department')->asArray()->all();
        return $this->render('exception',['models'=>$models]);
    }
}
