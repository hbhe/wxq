<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CorpSuite */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => 'Corp Suites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="corp-suite-create">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
