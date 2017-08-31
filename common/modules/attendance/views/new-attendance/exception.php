<?php
use yii\helpers\Html;
use common\modules\attendance\models\department;
use common\modules\attendance\models\user;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\attendance\models\NewAttendanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '异常人员名单统计';
$this->params['breadcrumbs'][] =$this->title;
$department=department::getDepartment(false);
$state=[1=>'在单位',2=>'不在单位'];
?>
<p>一个小时之内没有联系后台，或者不在单位的人员。</p>
<table class="table table-striped table-bordered table-hover">
	<th>序号</th><th>姓名</th><th>部门</th><th>电话号码</th><th>与后台联系时间</th><th>距现在时间</th><th>最后更新时的状态</th>
	<?php
		foreach ($models as $key => $model){
			$str='<tr>';
			$str.='<td>'.(++$key).'</td>';
			$str.='<td>'.$model['name'].'</td>';
			$str.='<td>'.user::idToName($model['department'],$department).'</td>';
			$str.='<td>'.$model['mobile'].'</td>';
			$str.='<td>'.$model['update_at'].'</td>';
			$str.='<td>'.round((time()-strtotime($model['update_at']))/3600,2).'小时</td>';
			$str.='<td>'.$state[$model['state']].'</td>';
			$str.='</tr>';
			echo $str;
		};
	?>
</table>