<?php 
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<?= Html::csrfMetaTags() ?>
	<title>会议请假审批</title>
	<link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
	</head>
	<body ontouchstart style="background-color: #f8f8f8;">
	<div class="weui-panel__bd">
        <div class="weui-media-box weui-media-box_text">
            <h4 class="weui-media-box__title"><?php echo $name ?>的会议请假</h4>
            <p class="weui-media-box__desc"><?php echo $msg ?></p>
            <ul class="weui-media-box__info">
                <li class="weui-media-box__info__meta">会议名称</li>
                <li class="weui-media-box__info__meta"><?php echo $meet['title'] ?></li>
            </ul>
        </div>
    </div>
    <div style="text-align:center">
                <a href="<?php echo \yii\helpers\Url::toRoute(['approve','name'=>$name,'userid'=>$userid,'meeting_id'=>$meet['id'],'msg'=>'1','agreen'=>'yes']); ?>" class="weui-btn weui-btn_mini weui-btn_primary">同意</a>　　
                <a href="<?php echo \yii\helpers\Url::toRoute(['approve','name'=>$name,'userid'=>$userid,'meeting_id'=>$meet['id'],'msg'=>'1','agreen'=>'no']); ?>" class="weui-btn weui-btn_mini weui-btn_warn">不同意</a>
            </div>
	</body>
</html>