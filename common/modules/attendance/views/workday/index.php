<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\widgets\Alert;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '工作日管理';
$this->params['breadcrumbs'][] = $this->title;
//记录当前页，方便编辑某页的某条数据后，再跳转到某页
setcookie('page',Yii::$app->request->get('page',1),time()+3600);
?>
<div class="workday-index">
    <p>
        如果当年的工作日已经增加，则增加下一年的工作日！新增工作日后，请按国家法定节假日，手动修改调休，比如五一、十一等等调整了周末的休息时间。<?= Html::a('新增一年的工作日', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           // 'id',
            'date',
            [
                'attribute'=>'is_work_day',
                'value'=>function($model){
                    $value=$model->getDay()[$model->is_work_day];
                    if($model->is_work_day==0){
                        return Html::tag('p', Html::encode($value), ['class' => 'text-danger']);
                    }else{
                        return Html::tag('p', Html::encode($value), ['class' => 'text-success']);
                    }
                   
                },
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn','template'=>'{update}','header' => '调休',],
        ],
    ]); ?>
</div>
