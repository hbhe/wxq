<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CorpAgent */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => 'Corp Agents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="corp-agent-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
