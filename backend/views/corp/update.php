<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Corp */

$this->title = '更新：' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Corps', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="corp-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
