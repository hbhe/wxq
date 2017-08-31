<?php
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\attendance\models\attendance;
use common\modules\attendance\models\department;
use common\modules\attendance\models\user;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\attendance\models\AttendanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '考勤';
$this->params['breadcrumbs'][] = $this->title;
$attendance=attendance::getAttendance();
unset($attendance[2]);//删除数组中的未考勤
$department=department::getDepartment(true);
?>
<div class="attendance-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'=>[
            'firstPageLabel'=>'第一页',
            'lastPageLabel'=>'最后一页',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'department',
                'value'=>function($model)use($department){
                    $id=$model->user->attributes['department'];
                    return user::idToName($id,$department);  
                },
                'filter'=>$department,
                'headerOptions' => ['width' => '150'] ,
            ],
            ['attribute'=>'name',
                'value'=>'user.name',
                'headerOptions' => ['width' => '120'] ,
               'filter'=>Html::activeTextInput($searchModel, 'name',['class'=>'form-control']) ,
            ],
            [
                'attribute'=>'attendance',
                'value'=>function($model)use($attendance){
                    return $attendance[$model->attendance];
                },
                'filter'=>$attendance,
                'headerOptions' => ['width' => '150'] ,
            ],
            [
                'attribute'=>'create_at',
                'value'=>function($model){return $model->create_at;},
                'filter'=>DatePicker::widget([ 
                    'name' => 'AttendanceSearch[create_at]', 
                    'options' => ['placeholder' => ''], 
                    //'value' => date('Y-m-d'), 
                    'pluginOptions' => [
                        'autoclose' => true, 
                        'format' => 'yyyy-mm-dd', 
                        'todayHighlight' => true 
                    ] 
                ]),
                //'headerOptions' => ['width' => '150'] ,
            ],
            [
                'attribute'=>'remarks',
                'value'=>function($model){
                    if($model->remarks=='0'){
                        return '手机长时间停放在单位';
                    }
                    if(is_numeric($model->remarks)){
                        return $model->remarks;
                    }
                }
            ],
            'kuanggong',
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'template'=>'{view}',
            //     'buttons'=>[
            //         'view'=>function($url,$model,$key){
            //             return Html::a('<span">详情列表</span>',Yii::$app->urlManager->createUrl(['attendance/fence/index','create_at'=>$model->create_at,'attendance_id'=>$key]),['title'=>'查看']);
            //         },
            //     ],
            //     'header'=>'进出单位详情',
            //     'headerOptions' => ['width' => '110'] ,
            // ],
        ],
    ]); ?>
</div>