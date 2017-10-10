<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\attendance\models\Meeting */
// $this->registerJsFile("https://www.helloweba.com/demo/js/my.js",['depends'=>['backend\assets\AppAsset'],'position'=>$this::POS_HEAD]);  
$this->registerJsFile("http://cdn.staticfile.org/jquery.qrcode/1.0/jquery.qrcode.min.js",['depends'=>['backend\assets\AppAsset'],'position'=>$this::POS_HEAD]);  
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '会议列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-view">
    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定删除此条记录吗?',
                'method' => 'post',
            ],
        ]) ?>
        <?php if(strtotime($model->meeting_time)>( time()+3600)): ?>
            <?php if(!isset($_COOKIE["meeting_id".$model->id]) || $_COOKIE["meeting_id".$model->id]!=1): ?>
                <?= Html::a('发布会议', ['publish', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?>
        <?php endif; ?>
    </p>
    <p>开会时间在一小时之后的会议才能发布；过期的会议，不能发布，只能重新修改会议时间才能发布；本条会议一天只能发布一次，隔天后可以重新发布，但会清除之前人员的确认状态，因此请慎重决定重复发送；发布会议后，可以更新会议的内容，确认参会人员收到的消息通知为最新的内容。</p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            // 'author',
            'title',
            'meeting_time',
            'addr',
            [
                'label'=>'未收到通知的参会人员:',
                'value'=>isset($participant[0]) ? implode($participant[0], ',') : '' ,
            ],
            [
                'label'=>'确认收到通知的参会人员:',
                'value'=>isset($participant[1]) ? implode($participant[1], ',') : '' ,
            ],
            [
                'label'=>'请假的参会人员:',
                'value'=>isset($participant[2]) ? implode($participant[2], ',') : '' ,
            ],
            [
                'label'=>'签到的参会人员:',
                'value'=>isset($participant[3]) ? implode($participant[3], ',') : '' ,
            ],
            'create_at',
            'content',
        ],
    ]) ?>

</div>
<h4>在微信中扫描此二维码，可以签到。鼠标右键点击图片，可以另存到本地（不可以复制）。或者点击按钮，在新窗口打开，直接在浏览器中打印！</h4>
<button id="saveImageBtn" class="btn btn-primary">新窗口打开图片</button>  
<p></p>
<div id="qrcode"></div>
<script>
    jQuery('#qrcode').qrcode({width: 500,height: 500,text: "<?php echo 'http://'.$_SERVER['HTTP_HOST'].\yii\helpers\Url::toRoute(['deal','meeting_id'=>$model->id,'status'=>3]) ?>"});


window.onload = function() {  
            var saveButton = document.getElementById("saveImageBtn");  
            bindButtonEvent(saveButton, "click", saveImageInfo);  
 
        };  
            function bindButtonEvent(element, type, handler)  
            {  
                   if(element.addEventListener) {  
                      element.addEventListener(type, handler, false);  
                   } else {  
                      element.attachEvent('on'+type, handler);  
                   }  
            }  
              
            function saveImageInfo ()   
            {  
                var mycanvas = document.getElementById("qrcode").firstChild;  
                var image    = mycanvas.toDataURL("image/png");  
                var w=window.open('about:blank','image from canvas');  
                w.document.write("<img src='"+image+"' alt='from canvas'/>");  
            }  
  
            function saveAsLocalImage () {  
                var myCanvas = document.getElementById("qrcode").firstChild;  
                // here is the most important part because if you dont replace you will get a DOM 18 exception.  
                // var image = myCanvas.toDataURL("image/png").replace("image/png", "image/octet-stream;Content-Disposition: attachment;filename=foobar.png");  
                var image = myCanvas.toDataURL("image/png").replace("image/png", "image/octet-stream");   
                window.location.href=image; // it will save locally  
            }  
</script>
