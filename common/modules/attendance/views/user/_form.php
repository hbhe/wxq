<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\attendance\models\department;
/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\user */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'leader')->dropDownList(department::getLeader()) ?>
	<?= $form->field($model, 'admin')->dropDownList(['0'=>'不是管理员','1'=>'管理员','2'=>'局长']) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
