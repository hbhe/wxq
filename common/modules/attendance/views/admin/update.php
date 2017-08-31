<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\admin */

$this->title = '修改管理员信息 ';
$this->params['breadcrumbs'][] = ['label' => 'Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' =>'查看详细页', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="admin-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
