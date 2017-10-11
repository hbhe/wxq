<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this \yii\web\View */
/* @var $content string */
$bundle = yii::$app->assetManager->getBundle(\frontend\assets\AgentAsset::className());
?>
<?php $this->title = '考勤和假期管理' ?>

<div class="page">
    <div class="page__hd">

        <h1 class="page__title"></h1>
        <p class="page__desc"></p>
        <p class="x">2017-04-04 12:30</p>
        <div class="weui-btn-area">
            <a id="formSubmitBtn" href="javascript:" class="weui-btn weui-btn_primary">打卡</a>
        </div>

    </div>

    <div class="page__bd" style="height: 100%;">
        <div class="weui-tab">
            <div class="weui-tab__panel">
                <div class="weui-grids">
                    <a href="javascript:;" class="weui-grid">
                        <div class="weui-grid__icon">
                            <img src="<?php echo $bundle->baseUrl . '/img/self/u2358.png'; ?>" alt="" >
                        </div>
                        <p class="weui-grid__label">请假申请</p>
                    </a>
                    <a href="javascript:;" class="weui-grid">
                        <div class="weui-grid__icon">
                            <img src="<?php echo $bundle->baseUrl . '/img/self/u2358.png'; ?>" alt="" >
                        </div>
                        <p class="weui-grid__label">外出申请</p>
                    </a>
                    <a href="<?php echo Url::to(['send-message']); ?>" class="weui-grid">
                        <div class="weui-grid__icon">
                            <img src="<?php echo $bundle->baseUrl . '/img/self/u2358.png'; ?>" alt="" >
                        </div>
                        <p class="weui-grid__label">发消息</p>
                    </a>

                </div>

            </div>
            <div class="weui-tabbar">
                <a href="javascript:;" class="weui-tabbar__item weui-bar__item_on">
                    <span style="display: inline-block;position: relative;">
                        <img src="<?php echo $bundle->baseUrl . '/img/self/u2358.png'; ?>" class="weui-tabbar__icon" >
                        <span class="weui-badge" style="position: absolute;top: -2px;right: -13px;">8</span>
                    </span>
                    <p class="weui-tabbar__label">首页</p>
                </a>
                <a href="javascript:;" class="weui-tabbar__item">
                    <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
                    <p class="weui-tabbar__label">考勤记录</p>
                </a>
                <a href="javascript:;" class="weui-tabbar__item">
                    <span style="display: inline-block;position: relative;">
                        <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
                        <span class="weui-badge weui-badge_dot" style="position: absolute;top: 0;right: -6px;"></span>
                    </span>
                    <p class="weui-tabbar__label">消息</p>
                </a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $('.weui-tabbar__item').on('click', function () {
            $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
        });
    });
</script>



