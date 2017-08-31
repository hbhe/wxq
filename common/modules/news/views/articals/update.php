<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\news\models\Articals */

$this->title = '修改: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
?>
<div class="articals-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
