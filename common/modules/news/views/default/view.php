<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?php echo $artical['title'] ?></title>
    <link rel="stylesheet" href="css/weui.css"/>
    <link rel="stylesheet" href="css/weui2.css"/>
    <link rel="stylesheet" href="css/weui3.css"/>
    <script src="js/zepto.min.js"></script>
    <script src="js/swipe.js"></script>
  </head>
  <body ontouchstart style="background-color: #f8f8f8;">
 <div class="weui-weixin">
  <div class="weui-weixin-ui">
  <!--页面开始-->
    <div class="weui-weixin-page">
   <h2 class="weui-weixin-title"><?php echo $artical['title'] ?></h2>
   <div class="weui-weixin-info"><!--meta-->
                     <!-- <span class="weui-weixin-em">最新消息</span> -->
                      <em class="weui-weixin-em" ><?php echo $artical['create_at'] ?></em>
                      <em class="weui-weixin-em">发布人</em>
                       <a class="weui-weixin-a weui-weixin-nickname" href="javascript:void(0);" ><?php echo $artical['author'] ?></a>
                    </div><!--meta结束-->
                    
    <div class="weui-weixin-img"><!--图片开始-->
    <img src="<?php echo \Yii::$app->imagemanager->getImagePath($artical['img'], 720); ?>">
    </div><!--图片结束-->
                                        
    <div class="weui-weixin-content"><!--内容-->
      <?php echo $artical['artical'] ?>

    <div class="weui-weixin-tools"><!--工具条-->
      <div class="weui-weixin-read">阅读: <span id="readnum"><?php echo $artical['click'] ?>次</span> </div>
    </div><!--工具条结束-->
</div><!--页面结束-->
</div>
</div>        
</body>
</html>