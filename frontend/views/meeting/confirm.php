<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>会议确认</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/weui/1.1.2/style/weui.min.css"/>
  </head>
    <body ontouchstart style="background-color: #f8f8f8;">
 		<img src="http://gs-admin.buy027.com/img/timg%20(1).png" style="width:100%" alt="">
    	<div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__bd">
                    <span style="vertical-align: middle">会议时间</span>
                    <span class="weui-badge" style="margin-left: 5px;background-color:#10aeff;font-size:16px"><?php echo $meeting->meeting_time; ?></span>
                </div>
                <div class="weui-cell__ft"></div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__bd">
                    <span style="vertical-align: middle">会议地点</span>
                    <span class="weui-badge" style="margin-left: 5px;background-color:#10aeff;font-size:16px"><?php echo $meeting->addr ?></span>
                </div>
                <div class="weui-cell__ft"></div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__bd">
                    <span style="vertical-align: middle">会议标题:</span>
                    <p><?php echo $meeting->title ?></p>
                </div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__bd">
                    <span style="vertical-align: middle">注意事项:</span>
                    <p><?php echo $meeting->content ?></p>
                </div>
            </div>
            <div style="text-align:center">
            	<a href="<?php echo \yii\helpers\Url::toRoute(['deal','userid'=>$userid,'meeting_id'=>$meeting->id,'status'=>1]); ?>" class="weui-btn weui-btn_mini weui-btn_primary">确认收到信息</a>　　
            	<a href="<?php echo \yii\helpers\Url::toRoute(['vacate','userid'=>$userid,'meeting_author'=>$meeting->author,'meeting_id'=>$meeting->id]); ?>" class="weui-btn weui-btn_mini weui-btn_warn">提交会议请假</a>
            </div>
            <!-- start -->
            <article class="weui-article">
            <section>
                <?php if(isset($participant[0])): ?>
                <section>
                    <h3>未收到通知的参会人员:</h3>
                    <p><?php echo implode($participant[0], ','); ?></p>
                </section>
                <?php endif; ?>
                <?php if(isset($participant[1])): ?>
                <section>
                    <h3>确认收到通知的参会人员:</h3>
                    <p><?php echo implode($participant[1], ','); ?></p>
                </section>
                <?php endif; ?>
                <?php if(isset($participant[2])): ?>
                <section>
                    <h3>请假的参会人员:</h3>
                    <p><?php echo implode($participant[2], ','); ?></p>
                </section>
                <?php endif; ?>
                <?php if(isset($participant[3])): ?>
                <section>
                    <h3>签到的参会人员:</h3>
                    <p><?php echo implode($participant[3], ','); ?></p>
                </section>
                <?php endif; ?>
            </section>
            </article>
            <!-- end -->
        </div>
    </body>
</html>