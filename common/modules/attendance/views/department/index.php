<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '部门列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-index">

    <p>
       在企业号通讯录中改动了部门，请点击“同步企业号部门列表”按钮，会自动同步企业通讯录<?= Html::a('同步企业号部门列表', ['sync'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'yii\grid\SerialColumn'],
            // 'id',
            [

                'attribute'=>'name',
                'value'=>function($model){
                    $num=substr_count($model->path, ',')-1;
                    return str_repeat('　　',$num).$model->name;
                }
            ]
            //'path',
            //'order',
        ],
    ]); ?>
</div>
