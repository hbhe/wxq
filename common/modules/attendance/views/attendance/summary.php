<?php
use kartik\date\DatePicker;
use yii\helpers\Html;
use common\modules\attendance\models\attendance;
use common\modules\attendance\models\department;
use common\modules\attendance\models\user;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\attendance\models\AttendanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '考勤';
$this->params['breadcrumbs'][] = $this->title;
$attendance=attendance::getAttendance();
$department=department::getDepartment(false,'');
?>
<div class="attendance-index">
    <div class="attendance-form">

    <?php $form = ActiveForm::begin(); ?>
        <?php
         echo DatePicker::widget([ 
                    'name' => 'from', 
                    'options' => ['placeholder' => '选择日期，会筛选出当月1日至选择日期的考勤统计'], 
                    'value' => date('Y-m-1'), 
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
                    'options' => ['placeholder' => '选择日期，会筛选出当月1日至选择日期的考勤统计'], 
                    'value' => date('Y-m-d',strtotime('-1 day')), 
                    'pluginOptions' => [
                        'autoclose' => true, 
                        'format' => 'yyyy-mm-dd', 
                        'todayHighlight' => true 
                    ] 
                ]);
        ?>
    <p><button class="btn btn-default" type="submit">查 询</button> 备注：如果当天日期考勤未结束的话请不要查询包含当天日期的考勤</p>
   <?php ActiveForm::end(); ?>
  </div>
  <p><?php echo $beginDate.'至'.$date; ?>共<?php echo $count ?>个工作日</p>
    <table class="table table-striped table-bordered table-hover">
    <th>姓名</th>
    <th>部门</th>
    <th>全勤天数</th>
    <th>迟到早退天数</th>
    <th>迟到早退（次数）</th>
    <th>请假</th>
    <th>休假</th>
    <th>旷工次数</th>
      <?php 
      foreach ($models as $model) {
          $str='<tr>';
          $str.='<td>'.$model['name'].'</td>';
          $str.='<td>'.user::idToName($model['department'],$department).'</td>';
          $str.='<td>'.$model['quanqin'].'</td>';
          $str.='<td>'.$model['chidao'].'</td>';
          $str.='<td>'.$model['bushiquanqin'].'</td>';
          $str.='<td>'.$model['qingjia'].'</td>';
          $str.='<td>'.$model['xiujia'].'</td>';
          $str.='<td>'.(($count-$model['chidao']-$model['quanqin']-$model['qingjia']-$model['xiujia'])*2+$model['kuanggong']).'</td>';
          $str.='</tr>';
          echo $str;
      }
     ?>  
    </table>
</div>