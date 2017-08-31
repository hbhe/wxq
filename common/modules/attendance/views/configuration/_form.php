<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\attendance\models\department;
use common\modules\attendance\models\configuration;
/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\configuration */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="configuration-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'department_id')->dropDownList(department::getDepartment(true)) ?>

    <?= $form->field($model, 'name')->dropDownList(configuration::getConfig()) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'corpid')->hiddenInput(['value' => Yii::$app->session->get('corpid')])->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
