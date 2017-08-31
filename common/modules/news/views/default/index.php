<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?php echo $banner_name ?></title>
    <link rel="stylesheet" href="css/weui.css"/>
    <link rel="stylesheet" href="css/weui2.css"/>
    <link rel="stylesheet" href="css/weui3.css"/>
    <script src="js/zepto.min.js"></script>
    <script src="js/swipe.js"></script>
    <script>
    $(function(){
      $('#slide1').swipeSlide({
      autoSwipe:true,//自动切换默认是
      speed:3000,//速度默认4000
      continuousScroll:true,//默认否
      transitionType:'cubic-bezier(0.22, 0.69, 0.72, 0.88)',//过渡动画linear/ease/ease-in/ease-out/ease-in-out/cubic-bezier
      lazyLoad:true,//懒加载默认否
      firstCallback : function(i,sum,me){
                  me.find('.dot').children().first().addClass('cur');
              },
              callback : function(i,sum,me){
                  me.find('.dot').children().eq(i).addClass('cur').siblings().removeClass('cur');
              }
      });
    }); 
    </script>
  </head>
    <body>
<?php if($banner_type==1): ?>
    <!-- 轮播图片（三张） -->
<div class="slide" id="slide1">
    <ul>
    <?php $key=0 ?>
    <?php foreach ($models as $v):?>
      <?php if($v['img']!=0): ?>
        <li>
            <a href="#">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" data-src="<?php echo \Yii::$app->imagemanager->getImagePath($v['img'], 9999, 9999); ?>" alt="">
            </a>
            <div class="slide-desc"><?php echo $v['title'] ?></div>
        </li>
        <?php
          $key++;//如果有图像就自增1
          if($key==3)break; //找出三张图片就跳出
         ?>
      <?php endif; ?>
    <?php endforeach; ?>
    </ul>
    <div class="dot">
        <span></span>
        <span></span>
       <span></span>
    </div>
</div>
<?php endif; ?>

<form name="myForm" action="<?php echo \yii\helpers\Url::toRoute(['index','banner_type'=>$banner_type,'corpid'=>$corpid]); ?>" method="post">
  <div class="weui_search_bar">
    <input name='search' type="search" class="search-input" id='search' placeholder='关键字' style="box-sizing:content-box"/><button  class="weui_btn weui_btn_mini weui_btn_primary" type="submit" value='submit' ><i class="icon icon-4"></i></button>
   </div> 
</form>
    <!-- 列表项 -->
    <!-- <div class="weui_cells_title">带说明的列表项</div> -->
    <div class="weui_cells">
    <?php if(empty($models)): ?>
      <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <p>没有搜索到您要查找的内容，请您重新更换搜索词！</p>
            </div>
        </div>
    <?php endif; ?>
      <?php foreach ($models as $k => $v):?>
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <p>
                <a href="<?php echo \yii\helpers\Url::toRoute(['view','id'=>$v['id']]); ?>">
                  <?php
                    if(mb_strlen($v['title'],'utf-8')<13) {//如果中文字符少于13个，则直接输出
                      echo $v['title'];
                    }else{//否则会截取12个
                      echo mb_substr($v['title'],0,12,'utf-8').'…';
                    }
                  ?>
                </a>
                </p>
            </div>
            <div class="weui_cell_ft">
                <?php echo '阅读：'.$v['click'].'次' ?>
            </div>
        </div>
      <?php endforeach; ?>

    </div>
    <div>
      <?php echo \yii\widgets\LinkPager::widget([
                'pagination' => $pages,
            ]);
 ?>
    </div>
<!-- 底部 -->
<div class="weui-footer">
  <p class="weui-footer__text">Copyright &copy; 2017-2020 技术支持：楚源盛互联网事业部</p>
</div>

    </body>
</html>