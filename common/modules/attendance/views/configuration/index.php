<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\attendance\models\department;
use common\modules\attendance\models\configuration;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配置列表';
$this->params['breadcrumbs'][] = $this->title;
//获取部门列表
$department=department::getDepartment();
//获取配置数组列表
$config=configuration::getConfig();
?>
<div class="configuration-index">

    <p>
        <?= Html::a('新增配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            [
                'attribute'=>'department_id',
                'value'=>function($model)use($department){
                    return $department[$model->department_id];
                }
            ],
            [
                'attribute'=>'name',
                'value'=>function($model)use($config){
                    return $config[$model->name];
                }
            ],
            'value',

            ['class' => 'yii\grid\ActionColumn','template'=>'{update} {delete}'],
        ],
    ]); ?>
</div>
