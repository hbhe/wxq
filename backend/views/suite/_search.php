<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SuiteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="suite-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sid') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'corp_id') ?>

    <?= $form->field($model, 'suite_id') ?>

    <?php // echo $form->field($model, 'suite_secret') ?>

    <?php // echo $form->field($model, 'suite_ticket') ?>

    <?php // echo $form->field($model, 'token') ?>

    <?php // echo $form->field($model, 'auth_code') ?>

    <?php // echo $form->field($model, 'permanent_code') ?>

    <?php // echo $form->field($model, 'accessToken') ?>

    <?php // echo $form->field($model, 'accessToken_expiresIn') ?>

    <?php // echo $form->field($model, 'encodingAESKey') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
