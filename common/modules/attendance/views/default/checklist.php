<?php 
use common\modules\attendance\models\NewAttendance;
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>审查列表</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
  </head>
  <body>
	<div class="page tabbar js_show">
	    <div class="page__bd" style="height: 100%;">
	        <div class="weui-tab">
	            <div class="weui-tab__panel"></div>
	            <div class="weui-tabbar">
	                <a href="<?php echo \yii\helpers\Url::toRoute(['check-list','userid'=>$userid,'filter'=>0]); ?>" class="weui-tabbar__item weui-bar__item_on">
	                    <span style="display: inline-block;position: relative;">
	                        <img src="./img/icon_tabbar.png" alt="" class="weui-tabbar__icon">
	                        <?php if($filter=='0'): ?>
	                        <span class="weui-badge" style="position: absolute;top: -2px;right: -13px;">
	                        <?php  echo count($vacate); ?></span>
	                    	<?php endif; ?>
	                    </span>
	                    <p class="weui-tabbar__label">未审核列表</p>
	                </a>
	                <a href="<?php echo \yii\helpers\Url::toRoute(['check-list','userid'=>$userid,'filter'=>1]); ?>" class="weui-tabbar__item">
	                    <img src="./img/icon_tabbar.png" alt="" class="weui-tabbar__icon">
	                    <p class="weui-tabbar__label">全部列表</p>
	                </a>
	            </div>
	        </div>
	    </div>
	</div>
	<div class="weui-panel">
            <div class="weui-panel__hd">审核列表</div>
            <div class="weui-panel__bd">
                <?php foreach ($vacate as $v):?>
                <div class="weui-media-box weui-media-box_text">
                    <h4 class="weui-media-box__title">
                    <?php 
			        	$submitter=isset($users[$v['submitter']]) ? $users[$v['submitter']] : '无 ';
			        	echo $submitter.'的“'.NewAttendance::attendance()[$v['vacate_type']].'”申请，'; 
			        	$str='申请类型出现错误';
			        	$leader='approver';
			        	if($v['approver']==$userid){
			        		if($v['approved']=='0'){
			        			$str='您没有审核。';
			        		}elseif($v['approved']=='1'){
			        			$str='您已经审核过。';
			        		}
			        	}
			        	if($v['reviewer']==$userid){
			        		$leader='reviewer';
			        		if($v['reviewed']=='0'){
			        			$str='您没有审批。';
			        		}elseif($v['reviewed']=='1'){
			        			$str='您已经审批过。';
			        		}
			        	}
			        	echo $str;
			        ?>
                    </h4>
                    <p class="weui-media-box__desc"><?php
			        	$str= $v['from_date'].'日至'.$v['to_date'].'日,';
			        	$dayOrHalf=NewAttendance::dayOrHalf();
			        	$str.=$dayOrHalf[$v['dayOrHalf']].'请假。<br>请假理由:'.$v['msg'];
			        	echo $str;
			        ?></p>
                    <ul class="weui-media-box__info">
                        <li class="weui-media-box__info__meta">申请时间</li>
                        <li class="weui-media-box__info__meta"><?php echo $v['create_at'] ?></li>
                        <li class="weui-media-box__info__meta weui-media-box__info__meta_extra"><?php echo Html::a('去审核>>', ['deal','id'=>$v['id'],'leader'=>$leader]); ?></li>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
    </div>
  </body>
</html>