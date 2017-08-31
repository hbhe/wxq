<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\news\models\Banners */

$this->title = '新增栏目';
$this->params['breadcrumbs'][] = ['label' => '栏目管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banners-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
