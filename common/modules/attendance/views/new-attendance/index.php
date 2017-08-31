<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use common\modules\attendance\models\NewAttendance;
    use common\modules\attendance\models\department;
    use common\modules\attendance\models\user;
    /* @var $this yii\web\View */
    /* @var $searchModel common\modules\attendance\models\NewAttendanceSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */
    $this->title = '考勤列表';
    $this->params['breadcrumbs'][] =$this->title;
    $attendance=NewAttendance::attendance();
    $department=department::getDepartment(true);
    echo Html::a('异常情况统计', ['exception'], ['class' => 'btn btn-success']);
    echo Html::a('请假统计', ['vacate'], ['class' => 'btn btn-success']);
?>
<div class="new-attendance-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'department',
                'value'=>function($model)use($department){
                    $id=$model->user->attributes['department'];
                    return user::idToName($id,$department);  
                },
                'filter'=>$department,
                'headerOptions' => ['width' => '130'] ,
            ],
            ['attribute'=>'name',
                'value'=>'user.name',
                'headerOptions' => ['width' => '110'] ,
               'filter'=>Html::activeTextInput($searchModel, 'name',['class'=>'form-control']) ,
            ],
            'work_date',
            [
                'attribute'=>'attendance',
                'value'=>function($model)use($attendance){
                    return $attendance[$model->attendance];
                },
                'filter'=>$attendance,
            ],
            [
                'attribute'=>'am_pm',
                'value'=>function($model){
                    if($model->am_pm==1){
                        return '上午';
                    }
                    return '下午';
                },
                'filter'=>[1=>'上午',2=>'下午'],
            ],
            'num',
            [
                'attribute'=>'start_at',
                'value'=>function($model){
                    if($model->start_at){
                        return date('Y-m-d H:i:s',$model->start_at);
                    }
                }
            ],
            [
                'attribute'=>'end_at',
                'value'=>function($model){
                    if($model->end_at){
                        return date('Y-m-d H:i:s',$model->end_at);
                    }
                }
            ],
            [
                'attribute'=>'state',
                'value'=>function($model){
                    if($model->state=='1'){
                        return '报警';
                    }
                },
                'filter'=>[1=>'报警',],
            ],
             [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',
                'buttons'=>[
                    'view'=>function($url,$model,$key){
                        return Html::a('<span>详情列表</span>',Yii::$app->urlManager->createUrl(['attendance/fence/index','create_at'=>$model->work_date,'attendance_id'=>$key]),['title'=>'查看']);
                    },
                ],
                'header'=>'进出单位详情',
                'headerOptions' => ['width' => '110'] ,
            ],
        ],
    ]); ?>
</div>
