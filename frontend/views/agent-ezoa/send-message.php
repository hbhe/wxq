<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$bundle = yii::$app->assetManager->getBundle(\frontend\assets\AgentAsset::className());
?>
<?php $this->title = '发消息' ?>

<div class="page">

    <div class="page__bd">

        <div id="form">
            <div class="weui-cells__title">性别</div>
            <div class="weui-cells weui-cells_radio">
                <label class="weui-cell weui-check__label" for="r1">
                    <div class="weui-cell__bd">男</div>
                    <div class="weui-cell__ft">
                        <input required type="radio" class="weui-check" name="sex" value="male" id="r1" tips="请选择性别">
                        <span class="weui-icon-checked"></span>
                    </div>
                </label>
                <label class="weui-cell weui-check__label" for="r2">
                    <div class="weui-cell__bd">女</div>
                    <div class="weui-cell__ft">
                        <input type="radio" name="sex" class="weui-check" value="female" id="r2">
                        <span class="weui-icon-checked"></span>
                    </div>
                </label>
            </div>

            <div class="weui-cells__title">编码助手(1-2个)</div>
            <div class="weui-cells weui-cells_checkbox">
                <label class="weui-cell weui-check__label" for="c1">
                    <div class="weui-cell__hd">
                        <input required pattern="{1,2}" type="checkbox" tips="请勾选1-2个敲码助手" class="weui-check" name="assistance" id="c1">
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">黄药师</div>
                </label>
                <label class="weui-cell weui-check__label" for="c2">
                    <div class="weui-cell__hd">
                        <input type="checkbox" name="assistance" class="weui-check" id="c2">
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">欧阳锋</div>
                </label>
                <label class="weui-cell weui-check__label" for="c3">
                    <div class="weui-cell__hd">
                        <input type="checkbox" name="assistance" class="weui-check" id="c3">
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">段智兴</div>
                </label>
                <label class="weui-cell weui-check__label" for="c4">
                    <div class="weui-cell__hd">
                        <input type="checkbox" name="assistance" class="weui-check" id="c4">
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">洪七公</div>
                </label>
            </div>
            <div class="weui-cells weui-cells_form">
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">手机号</label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="tel" required pattern="^\d{11}$" maxlength="11" placeholder="输入你现在的手机号" emptyTips="请输入手机号" notMatchTips="请输入正确的手机号">
                    </div>
                    <div class="weui-cell__ft">
                        <i class="weui-icon-warn"></i>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">身份证号码</label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" required pattern="REG_IDNUM" maxlength="18" placeholder="输入你的身份证号码" emptyTips="请输入身份证号码" notMatchTips="请输入正确的身份证号码">
                    </div>
                    <div class="weui-cell__ft">
                        <i class="weui-icon-warn"></i>
                    </div>
                </div>
                <div class="weui-cell weui-cell weui-cell_vcode">
                    <div class="weui-cell__hd"><label class="weui-label">验证码</label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" maxlength="4" type="text" required pattern="REG_VCODE" placeholder="点击验证码更换" tips="请输入验证码">
                    </div>
                    <div class="weui-cell__ft">
                        <i class="weui-icon-warn"></i>
                        <img class="weui-vcode-img" src="">
                    </div>
                </div>
            </div>
            <div class="weui-btn-area">
                <a id="formSubmitBtn" href="javascript:" class="weui-btn weui-btn_primary">提交</a>
            </div>
        </div>
        
    </div>


</div>
<script type="text/javascript">
/*
$("#id-div-tel-err").hide();

function vTele() {
	var obj = $("#tel");
	var value = obj.val();
	if(!(/^1\d{10}$/.test(value)))
	{
		obj.addClass("input_error");
        $("#id-div-tel").addClass( "weui-cell_warn");
        $("#id-div-tel-err").show();
		obj.focus();
		return false;
	}
	else
	{
		obj.removeClass("input_error");
        $("#id-div-tel").removeClass( "weui-cell_warn");
        $("#id-div-tel-err").hide();
		return true;
	}
}

function vCode() {
	var obj=$("#code");
	var value = obj.val();
    if (!value || !/\d{4}/.test(value))
	{
		obj.addClass("input_error");
		obj.focus();
		return false;
	}
	else
	{
		obj.removeClass("input_error");
		return true;
	}
}

var ajaxUrl = "<?= Url::to(['/redpack/default/ajax-broker']); ?>";

$(document).ready(function () {
    var smsVerify = function (mobileEle, verifyEle, verifyBtn) {
        var mobile = mobileEle.val();

        if (!vTele()) {
            weui.alert('无效的手机号码!');
            return false;
        }

        if ($(verifyBtn).hasClass('weui-btn_disabled')) {
            return false;
        }
        $(verifyBtn).addClass('weui-btn_disabled');

        $.ajax({
            url: '<?= Url::to(["/redpack/default/sms-ajax"], true) ?>',
            type: 'GET',
            data: 'mobile=' + mobile,
            dataType: 'json',
            cache: false,
            error: function (XHR, textStatus, errorThrown, err) {
                weui.alert('发送出错' + XHR.responseText);
                $(verifyBtn).removeClass('weui-btn_disabled');
            },
            success: function (data) {
                verifyEle.val('');
                if (data['code'] === 0) {
                    weui.toast("短信验证码已发送!");
                }
                else {
                    weui.toast(data['msg']);
                    $(verifyBtn).removeClass('weui-btn_disabled');
                }
            }
        });
    };

    $('#codeBtn').on('click', function (e) {
        smsVerify($('#tel'), $('#code'), this);
    });

    $("#okBtn").click(function () {
        var mobile = $('#tel').val();
        var verifycode = $('#code').val();
        if (!mobile || !/1[3|4|5|7|8]\d{9}/.test(mobile))
        {
            weui.toast("无效的手机号", 1000);
            return false;
        }

        if (!vCode()) {
            weui.toast("无效的验证码", 1000);
            return false;
        }

        var args = {
            'classname': '\\common\\models\\WxXgdxMember',
            'funcname': 'bindMobileAjax',
            'params': {
                'openid': '1',
                'mobile': mobile,
                'verifycode': verifycode
            }
        };

        $.ajax({
            url: ajaxUrl,
            type: "GET",
            cache: false,
            dataType: "json",
            data: "args=" + JSON.stringify(args),
            success: function (ret) {
                if (0 === ret['code']) {
                    location.href = "<?php // echo Url::to(['/redpack/default/profile']) ?>";
                }
                else {
                    weui.alert(ret['msg']);                    
                }
            },
            error: function () {
                weui.alert('系统错误');
            }
        });


    });


});

*/
</script>