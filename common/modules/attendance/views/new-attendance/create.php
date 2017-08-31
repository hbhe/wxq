<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\NewAttendance */

$this->title = 'Create New Attendance';
$this->params['breadcrumbs'][] = ['label' => 'New Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="new-attendance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
