<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '进出电子围栏信息表统计表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fence-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'attendance_id',
            'create_at',
            [
                'attribute'=>'pass',
                'value'=>function($model){
                   return $model->getPass($model->pass);
                }
            ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
