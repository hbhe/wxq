<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Corp */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Corps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="corp-view">

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
            'corp_id',
            'corp_name',
            'corp_type',
            'corp_round_logo_url:url',
            'corp_square_logo_url:url',
            'corp_user_max',
            'corp_agent_max',
            'corp_wxqrcode',
            'corp_full_name',
            'subject_type',
            'userid',
            'mobile',
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email:email',
            'access_token',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
