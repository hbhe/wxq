<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\configuration */

$this->title = '增加配置信息';
$this->params['breadcrumbs'][] = ['label' => '配置列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuration-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
