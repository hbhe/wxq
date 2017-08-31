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

$this->title = '考勤统计';
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
                    'value' => $beginDate, 
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
                    'value' => $date, 
                    'pluginOptions' => [
                        'autoclose' => true, 
                        'format' => 'yyyy-mm-dd', 
                        'todayHighlight' => true 
                    ] 
                ]);
        ?>
        <?= Html::dropDownList('department', $department_id,(['0'=>'全部']+$department), ['class' => 'form-control']); ?>
    <p><button class="btn btn-default" type="submit">查 询</button> 备注：如果当天日期考勤未结束的话请不要查询包含当天日期的考勤</p>
   <?php ActiveForm::end(); ?>
  </div>
  <p><?php echo $beginDate.'至'.$date; ?>共<?php echo $count ?>个工作日</p>
    <table class="table table-striped table-bordered table-hover">
    <th>序号</th>
    <th>姓名</th>
    <th>部门</th>
    <th>全勤次数</th>
    <th>迟到早退次数</th>
    <th>事假次数</th>
    <th>病假次数</th>
    <th>公休次数</th>
    <th>出差次数</th>
    <th>外出次数</th>
    <th>旷工次数</th>
    <th>其它次数</th>
    <th>迟到天数</th>
      <?php 
      $i=1;
      foreach ($users as $user) {
        $str='<tr>';
        $str.='<td>'.$i++.'</td>';
        $str.='<td>'.Html::a($user['name'],Yii::$app->urlManager->createUrl(['attendance/new-attendance/vacate','from'=>$beginDate,'to'=>$date,'userid'=>$user['userid']]),['title'=>'查看']).'</td>';
        $str.='<td>'.user::idToName($user['department'],$department).'</td>';
        if(isset($user['a'])){
          $str.='<td>'.$user['a']['quanqin'].'</td>';
          $str.='<td>'.($user['a']['chidao']+$user['a']['chidao2']).'</td>';
          $str.='<td>'.$user['a']['shijia'].'</td>';
          $str.='<td>'.$user['a']['bingjia'].'</td>';
          $str.='<td>'.$user['a']['gongxiu'].'</td>';
          $str.='<td>'.$user['a']['chuchai'].'</td>';
          $str.='<td>'.$user['a']['waichu'].'</td>';
          $str.='<td>'.($count*2-$user['a']['quanqin']-$user['a']['chidao_am_pm']-$user['a']['shijia']-$user['a']['bingjia']-$user['a']['gongxiu']-$user['a']['chuchai']-$user['a']['waichu']-$user['a']['qita']).'</td>';
          $str.='<td>'.$user['a']['qita'].'</td>';
          $str.='<td>'.$user['a']['chidao_am_pm'].'</td>';
        }else{
          $str.='<td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>'.($count*2).'</td><td>0</td><td>0</td>';
        }
        
        $str.='</tr>';
        echo $str;
      }
     ?>  
    </table>
</div>