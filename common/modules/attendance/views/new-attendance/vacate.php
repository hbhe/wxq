<?php 
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\modules\attendance\models\NewAttendance;
use yii\widgets\ActiveForm;
$this->title = '请假列表';
// $this->params['breadcrumbs'][] = $this->title;
 ?>
<div class="attendance-index">
    <div class="attendance-form">
    <?php $form = ActiveForm::begin(); ?>
        <?php
         echo DatePicker::widget([ 
                    'name' => 'from', 
                    'options' => ['placeholder' => '选择日期，会筛选出当月1日至选择日期的考勤统计'], 
                    'value' => $from, 
                    'pluginOptions' => [
                        'autoclose' => true, 
                        'format' => 'yyyy-mm-dd', 
                        'todayHighlight' => true 
                    ] 
                ]);
        ?>
        <?php
         echo DatePicker::widget([ 
                    'name' => 'to', 
                    'options' => ['placeholder' => '选择日期，会筛选出当月1日至选择日期的请假'], 
                    'value' => $to, 
                    'pluginOptions' => [
                        'autoclose' => true, 
                        'format' => 'yyyy-mm-dd', 
                        'todayHighlight' => true 
                    ] 
                ]);
        ?>
    <p><button class="btn btn-default" type="submit">查 询</button> </p>
   <?php ActiveForm::end(); ?>
</div>
<div class="vacate-index">
    <table class="table table-striped table-bordered table-hover">
    <th>序号</th>
    <th>请假人</th>
    <th>审核人</th>
    <th>审核</th>
    <th>审批人</th>
    <th>审批</th>
    <th>开始日期</th>
    <th>结束日期</th>
    <th>具体时间</th>
    <th>请假类型</th>
    <th>创建时间</th>
    <th style="width:300px;">请假理由</th>
      <?php 
      $i=1;
      $agree=[0=>'不同意',1=>'同意'];
      foreach ($vacates as $v) {
        $submitter=isset($users[$v['submitter']]) ? $users[$v['submitter']]:'无';
        $approver=isset($users[$v['approver']]) ?$users[$v['approver']]:'无';
        $reviewer=isset($users[$v['reviewer']]) ?$users[$v['reviewer']]:'无';
        $str='<tr>';
        $str.='<td>'.$i++.'</td>';
        $str.='<td>'.$submitter.'</td>';
        $str.='<td>'.$approver.'</td>';
        $str.='<td>'.$agree[$v['approved']].'</td>';
        $str.='<td>'.$reviewer.'</td>';
        $str.='<td>'.($reviewer=='无'?'' :$agree[$v['reviewed']]).'</td>';
        $str.='<td>'.$v['from_date'].'</td>';
        $str.='<td>'.$v['to_date'].'</td>';
        $str.='<td>'.NewAttendance::dayOrHalf()[$v['dayOrHalf']].'</td>';
        $str.='<td>'.NewAttendance::attendance()[$v['vacate_type']].'</td>';
        $str.='<td>'.$v['create_at'].'</td>';
        $str.='<td>'.$v['msg'].'</td>';
        $str.='</tr>';
        echo $str;
      }
     ?>  
    </table>
</div>