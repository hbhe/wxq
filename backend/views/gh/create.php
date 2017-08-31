<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WxGh */

$this->title = '接入新的公众号';
$this->params['breadcrumbs'][] = ['label' => '公众号', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-gh-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
