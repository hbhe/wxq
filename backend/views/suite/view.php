<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Suite */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Suites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="suite-view">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <p style="display:none;">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sid',
            'title',
            'corp_id',
            'suite_id',
            'suite_secret',
            'suite_ticket',
            'token',
            //'auth_code',
            //'permanent_code',
            //'accessToken',
            //'accessToken_expiresIn',
            'encodingAESKey',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
