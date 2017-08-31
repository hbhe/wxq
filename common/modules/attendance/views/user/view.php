<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\user */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?><?= Html::a('返回', ['index'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'userid',
            'name',
            'department',
            'position',
            'mobile',
            //'gender',
            'email:email',
            'weixinid',
            [
                'attribute'=>'avatar',
                'value'=> $model->avatar,
                'format' => ['image',['width'=>'80']],
            ],
            //'status',
            //'extattr',
           // 'leader',
            //'update_at',
            //'lng',
            //'lat',
        ],
    ]) ?>

</div>
