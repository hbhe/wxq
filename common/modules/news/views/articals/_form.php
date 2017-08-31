<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\news\models\Banners;
/* @var $this yii\web\View */
/* @var $model common\modules\news\models\Articals */
/* @var $form yii\widgets\ActiveForm */
//获取栏目名称
$banners=Banners::getBanners(Yii::$app->session->get('corpid'));
if(Yii::$app->session->get('auth')=='xinwen'){
    $banner_arr= [1=>$banners[1]];
}elseif(Yii::$app->session->get('auth')=='gonggao'){
    $banner_arr=[2=>$banners[2]];
}else{
    $banner_arr= $banners;
}
?>

<div class="articals-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'banner_id')->dropDownList($banner_arr) ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'img')->widget(\noam148\imagemanager\components\ImageManagerInputWidget::className(), [
        'aspectRatio' => 2,   // (16/9), (4/3)
        'showPreview' => true,
        'showDeletePickedImageConfirm' => false, //on true show warning before detach image
    ]); 
     ?>
    <?= $form->field($model, 'artical')->widget(\dosamigos\tinymce\TinyMce::className(), [
    'options' => ['rows' => 6],
    'language' => 'zh_CN',
    'clientOptions' => [
        'relative_urls' => false,
        'remove_script_host' => false,
        'convert_urls' => true,
        'file_browser_callback' => new yii\web\JsExpression("function(field_name, url, type, win) {
            window.open('".yii\helpers\Url::to(['/imagemanager/manager', 'view-mode'=>'iframe', 'select-type'=>'tinymce'])."&tag_name='+field_name,'','width=800,height=540 ,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no');
        }"),
        'plugins' => [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste image"
        ],
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    ]
    ]); 
 ?>

    <?= $form->field($model, 'create_at')->hiddenInput()->label(false) ?>
<!-- 满足客户要求，创建只能是未审核，领导审批才能正式发布 -->
    <?php if(Yii::$app->session->get('auth')=='admin'):  ?>
        <?= $form->field($model, 'issue')->dropDownList($model->isIssue()) ?>
    <?php else: ?>
        <?= $form->field($model, 'issue')->dropDownList(array(0=>'未审核')) ?>
    <?php endif; ?>
    
    <?= $form->field($model, 'order')->textInput() ?>

    <?= $form->field($model, 'corpid')->hiddenInput(['maxlength' => true])->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
