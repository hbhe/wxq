<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\news\models\Banners;
/* @var $this yii\web\View */
/* @var $model common\modules\news\models\Banners */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="banners-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_at')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'banner_type')->dropDownList(Banners::getType()) ?>

    <?= $form->field($model, 'order')->textInput() ?>

    <?= $form->field($model, 'corpid')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
