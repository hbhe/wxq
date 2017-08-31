<?php 
use common\modules\attendance\models\NewAttendance;
$tem_str='';
if($_GET['leader']=='approver'){
  if($vacate->approved==1){
    $tem_str='(已审核)！';
  }
}elseif ($_GET['leader']=='reviewer') {
    if($vacate->reviewed==1){
      $tem_str='(已审批)';
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>请假申请流程处理页面</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
  </head>
  <body>
  	<div class="weui-panel weui-panel_access">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_text">
                <h3 align="center"><?php echo $name ?>的请假申请<font color="red"><?php echo $tem_str ?></font></h3>
                <h4 class="weui-media-box__title">请假期限：<?php echo NewAttendance::dayOrHalf()[$vacate->dayOrHalf] ?></h4>
                <h4 class="weui-media-box__title">请假开始时间：<?php echo $vacate->from_date ?></h4>
                <h4 class="weui-media-box__title">请假结束时间：<?php echo $vacate->to_date ?></h4>
                <h4 class="weui-media-box__title">请假类型：<?php echo NewAttendance::attendance()[$vacate->vacate_type];?></h4>
                <h4 class="weui-media-box__title">请假原因：<?php echo $vacate->msg ?></h4>
            </div>
        </div>
    </div>
    <form name="myForm" action="<?php echo \yii\helpers\Url::toRoute(['deal','id'=>$vacate->id,'leader'=>$_GET['leader']]); ?>" method="post">
        <div class="page__bd">
          <div class="weui-cells__title">请选择是否批准</div>
          <div class="weui-cells weui-cells_radio">
              <label class="weui-cell weui-check__label" for="x11">
                  <div class="weui-cell__bd">
                      <p>同意</p>
                  </div>
                  <div class="weui-cell__ft">
                      <input type="radio" class="weui-check" name="agree" id="x11" value=1 checked="checked">
                      <span class="weui-icon-checked"></span>
                  </div>
              </label>
              <label class="weui-cell weui-check__label" for="x12">
                  <div class="weui-cell__bd">
                      <p>不同意</p>
                  </div>
                  <div class="weui-cell__ft">
                      <input type="radio" name="agree" class="weui-check" value=0 id="x12" >
                      <span class="weui-icon-checked"></span>
                  </div>
              </label>
          </div>
      </div>
      <?php if($tem_str==''): ?>
      <div class="weui_btn_area">
          <a onclick="submitForm()" class="weui-btn weui-btn_primary">提交</a>
      </div>
    <?php endif; ?>
    </form>
  </body>
</html>
<script type="text/javascript">
    function submitForm(){
        document.forms['myForm'].submit();
    }
</script>