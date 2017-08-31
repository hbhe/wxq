<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\news\models\Banners;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $name.'管理';
$this->params['breadcrumbs'][] = $this->title;
//获取栏目名称
$banners=Banners::getBanners(Yii::$app->session->get('corpid'));
?>
<div class="articals-index">
    <p>
        <?= Html::a('增加'.$name, ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute'=>'banner_id',
                'value'=>function($model)use($banners){
                    return $banners[$model->banner_id];
                }
            ],
            'author',
            'title',
            // [
            // 'attribute' =>'img',
            // 'format'=>[
            //     'image',
            //     [
            //     'width'=>'46',
            //     'height'=>'46'
            //     ]
            //     ],
            //     'value'=>function($model){
            //         var_dump($model);
            //         return empty($model->img) ? '' : \Yii::$app->imagemanager->getImagePath($model->img, 9999, 9999);
            //     }
            // ],
            'create_at',
            [
                'attribute'=>'issue',
                'value'=>function($model){
                    return $model->isIssue()[$model->issue];
                }
            ],
            // 'order',
            // 'corpid',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
