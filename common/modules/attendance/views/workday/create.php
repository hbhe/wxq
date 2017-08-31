<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\workday */

$this->title = '增加工作日';
$this->params['breadcrumbs'][] = ['label' => '工作日管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workday-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->dropDownList($model->getYear()) ?>

    <div class="form-group">
        <?= Html::submitButton('新增', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
