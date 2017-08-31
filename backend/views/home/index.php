<?php

/* @var $this yii\web\View */

$this->title = 'Applications';
?>
<div class="site-index">

    <div class="jumbotron hide">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>会议管理</h2>

                <p>参会管理，会议签名</p>

                <p>
<a class="btn btn-default" href="http://qy.weixin.qq.com/cgi-bin/3rd_loginpage?action=jumptoauthpage&suiteid=tj8c2445c93840db09$&t=wap">点此试用企业号第三方官网的会议套件 &raquo;</a>
<a href="https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id=wx0b4f26d460868a25&redirect_uri=wxe-admin.buy027.com"><img src="https://res.wx.qq.com/mmocbiz/en_US/tmt/home/dist/img/logo_blue_m_d179401b.png"></a>

<?php
$suite = 
$model = \common\models\Suite::findOne(['sid' => 'cys_meetings']);   
$we = $model->getQyWechat();
$suite_id = $model->suite_id;
$pre_auth_code = $we->setSuiteTicket($model->suite_ticket)->getPreAuthCode();
$redirect_uri = urlencode("http://wxe-admin.buy027.com/index.php");

$url = "https://qy.weixin.qq.com/cgi-bin/loginpage?suite_id=$suite_id&pre_auth_code=$pre_auth_code$&redirect_uri=$redirect_uri$&state=100";
?>
<a href="<?php echo $url ?>">goto</a>
                </p>
            </div>
            <div class="col-lg-4">
                <h2>企业文化</h2>

                <p>企业文化宣传</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">点此试用 &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>考勤请假</h2>

                <p>考勤请假审批等</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">点此试用 &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
