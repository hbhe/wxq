<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\Meeting */

$this->title = '更新: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '会议列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="meeting-update">
    <?= $this->render('_form', [
        'model' => $model,'authors'=>$authors
    ]) ?>
</div>
