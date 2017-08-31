<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\fence */

$this->title = 'Update Fence: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fences', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fence-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
