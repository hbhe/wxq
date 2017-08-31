<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CorpSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="corp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'corp_id') ?>

    <?= $form->field($model, 'corp_name') ?>

    <?= $form->field($model, 'corp_type') ?>

    <?= $form->field($model, 'corp_round_logo_url') ?>

    <?php // echo $form->field($model, 'corp_square_logo_url') ?>

    <?php // echo $form->field($model, 'corp_user_max') ?>

    <?php // echo $form->field($model, 'corp_agent_max') ?>

    <?php // echo $form->field($model, 'corp_wxqrcode') ?>

    <?php // echo $form->field($model, 'corp_full_name') ?>

    <?php // echo $form->field($model, 'subject_type') ?>

    <?php // echo $form->field($model, 'userid') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'username') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'access_token') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
