<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

use common\models\WxClient;
use common\models\WxGh;

/* @var $this yii\web\View */
/* @var $model common\models\WxGh */
/* @var $form yii\widgets\ActiveForm */

/*
$clients = WxClient::find()->orderBy('shortname')->all();
foreach ($clients as $client) {
    $radio_items[$client->id] =  $client->shortname;
}
*/
?>

<div class="wx-gh-form">    
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    
    <?php echo $form->field($model, 'sid')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'client_id')->dropDownList($radio_items) ?>
    
    <?= $form->field($model, 'gh_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'appId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'appSecret')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'encodingAESKey')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wxPayMchId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wxPayApiKey')->textInput(['maxlength' => true]) ?>
    
    <?php //echo $form->field($model, 'sms_template')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'platform')->dropDownList(WxGh::getPlatformOptionName()) ?>

    <?php //echo $form->field($model, 'is_service')->inline()->radioList(\common\wosotech\Util::getYesNoOptionName()); ?>

    <?php //echo $form->field($model, 'is_authenticated')->inline()->radioList(\common\wosotech\Util::getYesNoOptionName()); ?>

    <?php /* echo $form->field($model, 'qr_image_id')->widget(\noam148\imagemanager\components\ImageManagerInputWidget::className(), [
        'aspectRatio' => 1,   // (16/9), (4/3)
        'showPreview' => true,
        'showDeletePickedImageConfirm' => false, //on true show warning before detach image
    ]); */ ?>

    <?php //echo $form->field($model, 'wxmall_apiKey')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
