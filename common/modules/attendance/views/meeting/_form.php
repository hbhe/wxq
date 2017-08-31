<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\Meeting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meeting-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord): ?>
        <!-- 构造树形结构通讯录html -->
        请选择参会人员：
        <ul id="root">
                <?php 
                function procHtml($trees){
                    $html = '';
                    foreach($trees as $tree)
                    {
                        $html .='<li>';
                        $html .= '<label><input type="checkbox" name="department[]" value="'.$tree['id'].'"><a href="javascript:;">';
                        $html .= $tree['name'];
                        $html .='</a></label>';
                        $html .='<ul class="two"><li>';
                        if(!empty($tree['users'])){
                            foreach ($tree['users'] as $user) {
                                $html .='<li><label><input type="checkbox" name="users[]" value="'.$user['userid'].'"><a href="javascript:;">'.$user['name'].'</a></label></li>';
                            }
                        }
                        if($tree['parentid'] != ''){
                            $html .= procHtml($tree['parentid']);
                        }
                        $html .= '</li></ul>';
                        $html .='</li>';
                    }
                    return $html ? '<li>'.$html.'</li>' : $html ;
                }
                echo procHtml($AddressBook);
                ?>
        </ul>
        <!-- 树形结构通讯录end -->
    <?php endif; ?>
    <?= $form->field($model, 'author')->dropDownList($authors) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meeting_time')->widget(DateTimePicker::classname(), [ 
     'options' => ['placeholder' => '请选择开始时间...'],
     'pluginOptions' => [ 
              'autoclose' => true, 
              'todayHighlight' => true, 
              'format' =>'yyyy-mm-dd hh:ii:ss'
             ] 
    ]); ?>

    <?= $form->field($model, 'addr')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'content')->textarea(['rows'=>3]) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
