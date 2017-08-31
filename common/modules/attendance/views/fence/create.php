<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\fence */

$this->title = 'Create Fence';
$this->params['breadcrumbs'][] = ['label' => 'Fences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fence-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
