<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\NewAttendance */

$this->title = 'Update New Attendance: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'New Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="new-attendance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
