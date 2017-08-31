<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\workday */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="workday-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->dropDownList($model->getYear()) ?>

    <div class="form-group">
        <?= Html::submitButton('新增', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
