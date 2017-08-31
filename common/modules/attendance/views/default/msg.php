<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>提示页</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
  </head>
<body>
    <div class="weui-msg">
        <div class="weui-msg__icon-area">
        <?php if(isset($msg) && strpos($msg,'成功') !=false): ?>
            <i class="weui-icon-success weui-icon_msg"></i>
        <?php else: ?>
            <i class="weui-icon-info weui-icon_msg"></i>
        <?php endif; ?>
        </div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title"><?php echo isset($msg) ? $msg : '操作成功'; ?></h2>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:WeixinJSBridge.call('closeWindow');" class="weui-btn weui-btn_primary">关闭页面</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__text">Copyright &copy; 2017-2020 技术支持：楚源盛互联网事业部</p>
            </div>
        </div>
    </div>
</body>
</html>