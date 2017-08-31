<?php
use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会议管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-index">
    <p>
        <?= Html::a('新增会议', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // 'id',
            // 'author',
            'title',
            'meeting_time',
            'addr',
            'create_at',
            // 'content',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
