<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model common\modules\news\models\Articals */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="articals-view">
    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除此条文章?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'author',
            'title',
            [
                'attribute' =>'img',
                'format'=>[
                    'image',
                    [
                        'width'=>'100',
                       // 'height'=>'46'
                    ]
                ],

                'value'=>function($model){
                    return empty($model->img) ? '' : \Yii::$app->imagemanager->getImagePath($model->img, 9999, 9999);
                }
            ],
            'artical:html',
            'create_at',
            //'issue',
            // 'order',
            // 'corpid',
        ],
    ]) ?>

</div>
