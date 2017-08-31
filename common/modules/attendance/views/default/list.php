<?php 
use common\modules\attendance\models\NewAttendance;
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>请假申请列表</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
  </head>
  <body>
  	<div class="page__bd" style="height: 100%;">
        <div class="weui-tab">
            <div class="weui-navbar">
            	<?php if($name[$userid]['leader']<10){
            		echo '<div class="weui-navbar__item">';
			  		echo Html::a('审核审批表', ['check-list','userid'=>$userid]);
			  		echo '</div>';
            	}
			  	?>
                <div class="weui-navbar__item">
                	<?php echo Html::a('请假页面', ['index']);?>
                </div>
            </div>
            <div class="weui-tab__panel">
            </div>
        </div>
    </div>
<div class="weui-panel">
  	<div class="weui-panel__hd">您的请假列表</div>
  	<div class="weui-panel__bd">
  	<?php foreach ($vacate as $v):?>
	    <div class="weui-media-box weui-media-box_text">
	        <h4 class="weui-media-box__title"><?php 
	        	$approver=isset($name[$v['approver']]) ? $name[$v['approver']]['name'] : '';
	        	echo NewAttendance::attendance()[$v['vacate_type']].'申请，';
	        	if($v['approved']=='1'){
	        		echo $approver.'审核通过!';
	        	}else{
	        		echo $approver.'未审核!';
	        	}
	        	$reviewer=isset($name[$v['reviewer']]) ? $name[$v['reviewer']]['name'] :'';
	        	if($v['reviewer'] !='0'){
	        		if($v['reviewed']==1){
		        		echo $reviewer.'审批通过！';
		        	}else{
		        		echo $reviewer.'未审核！';
		        	}
	        	}
	        ?></h4>
	        <p class="weui-media-box__desc">
	        <?php
	        	$str= $v['from_date'].'日至'.$v['to_date'].'日,';
	        	$dayOrHalf=NewAttendance::dayOrHalf();
	        	$str.=$dayOrHalf[$v['dayOrHalf']].'请假。<br>请假理由：';
	        	$str.=$v['msg'];
	        	echo $str;
	        ?></p>
	        <ul class="weui-media-box__info">
                <li class="weui-media-box__info__meta">申请时间</li>
                <li class="weui-media-box__info__meta"><?php echo $v['create_at'] ?></li>
            </ul>
	    </div>
	<?php endforeach; ?>
	</div>
</div>
  </body>
</html>