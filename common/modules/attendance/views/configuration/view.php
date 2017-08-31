<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\configuration */

$this->title = '查看详细参数';
$this->params['breadcrumbs'][] = ['label' => '配置列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuration-view">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定删除此条配置信息吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'department_id',
            'name',
            'value',
        ],
    ]) ?>

</div>
