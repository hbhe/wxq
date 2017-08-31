<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?php echo $user->name; ?>的请假申请</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
  </head>
<body>
<!-- <form name="myForm" action="<?php // echo \yii\helpers\Url::toRoute(['index','banner_type'=>$banner_type,'corpid'=>$corpid]); ?>" method="post"> -->
<form name='myForm' action="" method="post">
审批和审核人：
<?php foreach ($models as $model): ?>
<?php echo $model['name'].' ' ?>
<input type="hidden" name="checker[]" value="<?php echo $model['userid']; ?>">
<?php endforeach; ?>
<input type="hidden" name="submitter" value="<?php echo $user->userid; ?>">
<input type="hidden" name="submitterName" value="<?php echo $user->name; ?>">
<div class="weui-cells weui-cells_radio">
    <div class="weui-cells__title">请假时间</div>
    <label class="weui-cell weui-check__label" for="x11">
        <div class="weui-cell__bd">
            <p>全天请假</p>
        </div>
        <div class="weui-cell__ft">
            <input type="radio" class="weui-check" name="dayOrHalf" value="0" id="x11" checked="checked">
            <span class="weui-icon-checked"></span>
        </div>
    </label>
    <label class="weui-cell weui-check__label" for="x12">
        <div class="weui-cell__bd">
            <p>上午请假</p>
        </div>
        <div class="weui-cell__ft">
            <input type="radio" name="dayOrHalf" class="weui-check" value="1" id="x12" >
            <span class="weui-icon-checked"></span>
        </div>
    </label>
    <label class="weui-cell weui-check__label" for="x13">
        <div class="weui-cell__bd">
            <p>下午请假</p>
        </div>
        <div class="weui-cell__ft">
            <input type="radio" name="dayOrHalf" class="weui-check" value="2" id="x13" >
            <span class="weui-icon-checked"></span>
        </div>
    </label>
</div>
<div class="weui-cell">
    <div class="weui-cell__hd"><label for="" class="weui-label">开始日期</label></div>
    <div class="weui-cell__bd">
        <input class="weui-input" name="from_date" type="date" value="" placeholder="">
    </div>
</div>
<div class="weui-cell">
    <div class="weui-cell__hd"><label for="" class="weui-label">结束日期</label></div>
    <div class="weui-cell__bd">
        <input class="weui-input" name="to_date" type="date" value="" placeholder="">
    </div>
</div>
<div class="weui-cells__title">选择请假类型：</div>
<div class="weui-cell weui-cell_select">
    <div class="weui-cell__bd">
        <select class="weui-select" name="type">
            <option value="3">事假</option>
            <option value="4">病假</option>
            <option value="5">公休</option>
            <option value="6">出差</option>
            <option value="7">外出</option>
            <option value="8">其它</option>
        </select>
    </div>
</div>
<div class="weui-cell">
    <div class="weui-cell__bd">
        <textarea class="weui-textarea" name='msg' placeholder="请输入请假理由" rows="3"></textarea>
        <div class="weui-textarea-counter"><span>0</span>/200</div>
    </div>
</div>
<a href="#" class="weui-btn weui-btn_primary" onclick="submitForm()">提交申请</a>

</form>
<!-- 提交表单，检查 -->
<script type="text/javascript">
    function submitForm(){
        if(document.getElementsByName("from_date")[0].value =='' ){
            alert("请假开始日期没有选择!");
            return false;
        }
        if(document.getElementsByName("to_date")[0].value =='' ){
            alert("请假结束日期没有选择!");
            return false;
        }
        if(document.getElementsByName("msg")[0].value =='' ){
            alert("没有填写请假理由!");
            return false;
        }
        document.forms['myForm'].submit();
    }
</script>
        <!-- 底部 -->
        <div class="weui-footer">
          <p class="weui-footer__text">Copyright &copy; 2017-2020 技术支持：楚源盛互联网事业部</p>
        </div>
    </body>
</html>