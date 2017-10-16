<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$bundle = yii::$app->assetManager->getBundle(\frontend\assets\AgentAsset::className());
\yii\bootstrap\BootstrapAsset::register($this);
?>
<?php $this->title = '发消息' ?>

<?php $form = ActiveForm::begin([
    'enableClientScript' => false,
    'fieldConfig' => [
        'template' => "{input}{error}",
        //'template' => "{input}",
        'options' => ['tag' => false],
    ],
]); ?>

<?=  \common\widgets\JsTree::widget([
    'options' => [
        'id' => 'tree_id',
    ],
    'name' => 'js_tree',
    'core' => [
        //'check_callback' => true,
        //'multiple' => false,
        /*
        'data' => [
            ['id' => 'ajson1', 'parent' => '#', 'text' => '根结点'],
            ['id' => 'ajson2', 'parent' => '#', 'text' => '根结点2'],
            ['id' => 'ajson3', 'parent' => 'ajson2', 'text' => '儿子1'],
            ['id' => 'ajson4', 'parent' => 'ajson2', 'text' => 'child2', 'icon' => '', ],
        ],

        'data' => [
            ['id' => 'ajson-1', 'text' => '根结点-1', ],
            ['id' => 'ajson-2', 'text' => '根结点-2', 'children' => [
                ['id' => 'ajson3', 'children' => [], 'text' => '儿子1'],
                ['id' => 'ajson4', 'children' => [], 'text' => 'child2', 'icon' => ''],
            ]],
        ],
        'data' => [
            'url' => \yii\helpers\Url::to(['ajax/tree']),
        ],

        'themes' => [
            'name' => 'foobar',
            'url' => "/themes/foobar/js/jstree3/style.css",
            'dots' => true,
            'icons' => false,
        ]

        */
        'data' => $jsTreeData,
    ],
    'types' => [
        'default' => [
        ],
        'employee' => [
            'icon' => 'glyphicon glyphicon-user',
        ],
    ],

    'plugins' => ['types', 'dnd', 'contextmenu', 'wholerow', 'state', 'checkbox'],

]); ?>

<div class="page">

    <div class="page__bd">

        <div class="weui-cells__title">单选列表项</div>
        <div class="weui-cells weui-cells_radio">
            <label class="weui-cell weui-check__label" for="x11">
                <div class="weui-cell__bd">
                    <p>cell standard</p>
                </div>
                <div class="weui-cell__ft">
                    <input type="radio" class="weui-check" name="radio1" id="x11"/>
                    <span class="weui-icon-checked"></span>
                </div>
            </label>

            <label class="weui-cell weui-check__label" for="x12">
                <div class="weui-cell__bd">
                    <p>cell standard</p>
                </div>
                <div class="weui-cell__ft">
                    <input type="radio" name="radio1" class="weui-check" id="x12" checked="checked"/>
                    <span class="weui-icon-checked"></span>
                </div>
            </label>
            <a href="javascript:void(0);" class="weui-cell weui-cell_link">
                <div class="weui-cell__bd">添加更多</div>
            </a>
        </div>

        <div class="weui-cells__title">复选列表项</div>
        <div class="weui-cells weui-cells_checkbox">
            <label class="weui-cell weui-check__label" for="s11">
                <div class="weui-cell__hd">
                    <input type="checkbox" class="weui-check" name="checkbox1" id="s11" checked="checked"/>
                    <i class="weui-icon-checked"></i>
                </div>
                <div class="weui-cell__bd">
                    <p>standard is dealt for u.</p>
                </div>
            </label>
            <label class="weui-cell weui-check__label" for="s12">
                <div class="weui-cell__hd">
                    <input type="checkbox" name="checkbox1" class="weui-check" id="s12"/>
                    <i class="weui-icon-checked"></i>
                </div>
                <div class="weui-cell__bd">
                    <p>standard is dealicient for u.</p>
                </div>
            </label>
            <a href="javascript:void(0);" class="weui-cell weui-cell_link">
                <div class="weui-cell__bd">添加更多</div>
            </a>
        </div>

        <div class="weui-cells__title">表单</div>
        <div class="weui-cells weui-cells_form">

            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">QQ号码</label></div>
                <div class="weui-cell__bd">
                    <!--
                    <input class="weui-input" type="number" pattern="[0-9]*" placeholder="请输入qq号"/>
                    -->
                    <?php echo $form->field($model, 'corp_id')->textInput(['class' => 'weui-input', 'type' => 'number', 'pattern' => '1[0-9]*', 'placeholder' => '请输入qq号']) ?>
                </div>
            </div>

            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd">
                    <label class="weui-label">手机号</label>
                </div>
                <div class="weui-cell__bd">
                    <!--
                    <input class="weui-input" type="tel" placeholder="请输入手机号"/>
                    -->
                    <?php echo $form->field($model, 'corp_id')->textInput(['class' => 'weui-input', 'type' => 'tel', 'pattern' => '1[0-9]*', 'placeholder' => '请输入手机号码']) ?>
                </div>
                <div class="weui-cell__ft">
                    <button class="weui-vcode-btn">获取验证码</button>
                </div>
            </div>

            <div class="weui-cell">
                <div class="weui-cell__hd"><label for="" class="weui-label">日期</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="date" value=""/>
                </div>
            </div>

            <div class="weui-cell">
                <div class="weui-cell__hd"><label for="" class="weui-label">时间</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="datetime-local" value="" placeholder=""/>
                </div>
            </div>

            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd"><label class="weui-label">验证码</label></div>
                <div class="weui-cell__bd">
                    <!--
                    <input class="weui-input" type="number" placeholder="请输入验证码"/>
                    -->
                    <?php echo $form->field($model, 'corp_id')->textInput(['class' => 'weui-input', 'type' => 'number', 'pattern' => '1[0-9]*', 'placeholder' => '请输入验证码']) ?>
                </div>
                <div class="weui-cell__ft">
                    <img class="weui-vcode-img" src="./images/vcode.jpg" />
                </div>
            </div>
        </div>

        <div class="weui-cells__tips">底部说明文字底部说明文字</div>

        <div class="weui-cells__title">表单报错</div>
        <div class="weui-cells weui-cells_form">
            <div class="weui-cell weui-cell_warn">
                <div class="weui-cell__hd"><label for="" class="weui-label">卡号</label></div>
                <div class="weui-cell__bd">
                    <!--
                    <input class="weui-input" type="number" pattern="[0-9]*" value="weui input error" placeholder="请输入卡号"/>
                    -->
                    <?php echo $form->field($model, 'corp_id')->textInput(['class' => 'weui-input', 'type' => 'number', 'pattern' => '[0-9]*', 'placeholder' => '请输入卡号']) ?>

                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon-warn"></i>
                </div>
            </div>
        </div>


        <div class="weui-cells__title">开关</div>
        <div class="weui-cells weui-cells_form">
            <div class="weui-cell weui-cell_switch">
                <div class="weui-cell__bd">勾选</div>
                <div class="weui-cell__ft">
                    <!--
                    <input class="weui-switch" type="checkbox"/>
                    -->
                    <?php echo $form->field($model, 'status')->checkbox(['class' => 'weui-switch'], false) ?>

                </div>
            </div>
            <div class="weui-cell weui-cell_switch">
                <div class="weui-cell__bd">切换按钮1</div>
                <div class="weui-cell__ft">
                    <label for="switchCP" class="weui-switch-cp">
<!--
                        <input id="switchCP" class="weui-switch-cp__input" type="checkbox" checked="checked"/>
-->
                        <?php echo $form->field($model, 'status')->checkbox(['id' => 'switchCP', 'class' => 'weui-switch-cp__input'], false) ?>

                        <div class="weui-switch-cp__box"></div>
                    </label>
                </div>
            </div>
        </div>

        <div class="weui-cells__title">文本框</div>
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" placeholder="请输入文本"/>
                </div>
            </div>
        </div>

        <div class="weui-cells__title">文本域</div>
        <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <textarea class="weui-textarea" placeholder="请输入文本" rows="3"></textarea>
                    <div class="weui-textarea-counter"><span>0</span>/200</div>
                </div>
            </div>
        </div>

        <div class="weui-cells__title">选择</div>
        <div class="weui-cells">

            <div class="weui-cell weui-cell_select weui-cell_select-before">
                <div class="weui-cell__hd">
                    <!--
                    <select class="weui-select" name="select2">
                        <option value="1">+86</option>
                        <option value="2">+80</option>
                        <option value="3">+84</option>
                        <option value="4">+87</option>
                    </select>
                    -->
                    <?= $form->field($model, 'corp_id')->dropDownList(\common\wosotech\Util::getYesNoOptionName(), ['class' => 'weui-select']) ?>

                </div>
                <div class="weui-cell__bd">
                    <!--
                    <input class="weui-input" type="number" pattern="[0-9]*" placeholder="请输入号码"/>
                    -->
                    <?php echo $form->field($model, 'corp_id')->textInput(['class' => 'weui-input', 'maxlength' => true, 'type' => 'number', 'pattern' => '[0-9]*', 'placeholder' => '请输入号码']) ?>
                </div>
            </div>
        </div>
        <div class="weui-cells__title">联系方式</div>
        <div class="weui-cells">
            <div class="weui-cell weui-cell_select">
                <div class="weui-cell__bd">
                    <!--
                    <select class="weui-select" name="select1">
                        <option selected="" value="1">微信号</option>
                        <option value="2">QQ号</option>
                        <option value="3">Email</option>
                    </select>
                    -->
                    <?= $form->field($model, 'corp_id')->dropDownList(\common\wosotech\Util::getYesNoOptionName(), ['class' => 'weui-select']) ?>

                </div>
            </div>

            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd">
                    <label for="" class="weui-label">国家/地区</label>
                </div>
                <div class="weui-cell__bd">
                    <!--
                    <select class="weui-select" name="select2">
                        <option value="1">中国</option>
                        <option value="2">美国</option>
                        <option value="3">英国</option>
                    </select>
                    -->
                    <?= $form->field($model, 'corp_id')->dropDownList(\yii\helpers\ArrayHelper::map(
                        \common\models\Suite::find()->where(['corp_id' => yii::$app->params['corp_id']])->all(),
                        'suite_id',
                        'sid'
                    ), ['prompt'=>'---', 'class' => 'weui-select']) ?>

                </div>
            </div>

        </div>

        <label for="weuiAgree" class="weui-agree">
            <input id="weuiAgree" type="checkbox" class="weui-agree__checkbox"/>
            <span class="weui-agree__text">
                阅读并同意<a href="javascript:void(0);">《相关条款》</a>
            </span>
        </label>

        <div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">确定</a>
        </div>


    </div>


</div>
<?php ActiveForm::end(); ?>

<?php /*
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


</script>
*/
