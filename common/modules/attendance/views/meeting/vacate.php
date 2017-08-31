<?php 
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<?= Html::csrfMetaTags() ?>
	<title>会议请假</title>
	<link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
	</head>
	<body ontouchstart style="background-color: #f8f8f8;">
	<form name='myForm' action="<?php echo \yii\helpers\Url::toRoute(['vacate','userid'=>$userid,'meeting_author'=>$meeting_author,'meeting_id'=>$meeting_id]) ?>" method="post">
		<div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                	<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>"> 
                    <textarea name='msg' class="weui-textarea" placeholder="请输入文本" rows="3"></textarea>
                    <div class="weui-textarea-counter"><span>0</span>/200</div>
                </div>
            </div>
        </div>
		<div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" href="#" onclick="submitForm()" id="showTooltips">确定</a>
        </div>
    </form>
	</body>
</html>
<!-- 提交表单，检查 -->
<script type="text/javascript">
    function submitForm(){
        if(document.getElementsByName("msg")[0].value =='' ){
            alert("请输入请假理由!");
            return false;
        }
        document.forms['myForm'].submit();
    }
</script>