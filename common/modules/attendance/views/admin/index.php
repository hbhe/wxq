<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '管理员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'admin',
            //'password',
            // 'corpid',
            //'secret',
            'login_at',
            'login_ip',

            ['class' => 'yii\grid\ActionColumn','template' => '{update} 　{view}'],
        ],
    ]); ?>
</div>
