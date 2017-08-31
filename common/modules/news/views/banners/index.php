<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\news\models\Banners;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '栏目管理';
$this->params['breadcrumbs'][] = $this->title;
//获取栏目类型
$type=Banners::getType();
?>
<div class="banners-index">

    <p>
        <?= Html::a('新增栏目', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'create_at',
            [
                'attribute'=>'banner_type',
                'value'=>function($model)use($type){
                    return $type[$model->banner_type];
                }
            ],
            //'order',
            // 'corpid',

            ['class' => 'yii\grid\ActionColumn','header'=>'操作'],
        ],
    ]); ?>
</div>
