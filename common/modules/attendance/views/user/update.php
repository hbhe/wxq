<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\user */

$this->title = '修改权限';
$this->params['breadcrumbs'][] = ['label' => '职工', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="user-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
