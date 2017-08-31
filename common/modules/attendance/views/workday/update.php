<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\workday */

$this->title = '更新工作日';
$this->params['breadcrumbs'][] = ['label' => '工作日管理列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="workday-update">

<div class="workday-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'is_work_day')->dropDownList($model->getDay()) ?>

    <div class="form-group">
        <?= Html::submitButton('修改', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
