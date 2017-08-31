<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\news\models\Articals */

$this->title = '新增';
$this->params['breadcrumbs'][] = ['label' => '列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="articals-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
