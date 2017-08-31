<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

use common\models\WxUser;
use common\models\WxTemplateId;
use common\models\Y;
/*
use common\models\wxpay\WxPayException;
use common\models\wxpay\WxPayDataBase;
use common\models\wxpay\WxPayResults;
use common\models\WxApiException;
use common\models\wxcard\Signature;
*/
use yii\helpers\Url;

/**
 * This is the model class for table "wx_gh".
 *
 * @property string $gh_id
 * @property string $appId
 * @property string $appSecret
 * @property string $token
 * @property string $accessToken
 * @property integer $accessToken_expiresIn
 * @property string $encodingAESKey
 * @property integer $encodingMode
 * @property string $wxPayMchId
 * @property string $wxPayApiKey
 * @property string $wxmall_apiKey
 * @property string $sms_template
 * @property integer $created_at
 * @property integer $updated_at
 */
class WxGh extends \yii\db\ActiveRecord {

//    use HuilongTrait;

    const WXGH_XGDXFW = 'gh_82df8393167b';
    const WXGH_XNDXFW = 'gh_dacd2ee0dede';
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%gh}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['gh_id', 'appId', 'appSecret', 'sid'], 'required'],
            [['accessToken_expiresIn', 'encodingMode', 'client_id'], 'integer'],
            [['sms_template'], 'string', 'max' => 12],
            [['gh_id', 'token', 'wxPayMchId'], 'string', 'max' => 32],
            [['appId', 'appSecret', 'wxPayApiKey'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['appId', 'sid'], 'unique'],            
            [['encodingAESKey'], 'string', 'max' => 43],
            
            ['wxcardapiTicket_expiresIn', 'safe'],
//            [['platform', 'is_service', 'is_authenticated', 'qr_image_id'], 'integer'],            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'sid' => '识别号',                    
            'title' => '公众号名称',
            'gh_id' => '公众号原始ID',
            'appId' => '公众号AppID（应用ID）',
            'appSecret' => '公众号AppSecret（应用密钥）',
            'token' => 'Token（令牌）',
            'accessToken' => '访问令牌',
            'accessToken_expiresIn' => '访问令牌失效时间',
            'encodingAESKey' => 'EncodingAESKey（消息加解密密钥）',
            'encodingMode' => '消息加解密方式',
            'wxPayMchId' => '微信支付商户ID',
            'wxPayApiKey' => '微信支付API密钥',
            'sms_template' => '短信模板ID',
            'client_id' => '所属客户',
            'clientName' => '所属客户',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'platform' => '平台',           // 0:WXP, 1:汇龙
            'is_service' => '服务号',   // 0: 订阅号, 1:服务号
            'is_authenticated' => '已认证', // 0:未认证, 1:已认证      
            'qr_image_id' => '关注二维码',
        ];
    }

    public function behaviors() {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    public function beforeSave($insert) {
        if ($insert) {
            $this->token = $this->token ?: Y::randomString(Y::RANDOM_NONCESTRING, 16);
            $this->encodingAESKey = $this->encodingAESKey ?: Y::randomString(Y::RANDOM_NONCESTRING, 43);
        }
        if (empty($this->wxPayApiKey)) {
            $this->wxPayApiKey = Y::randomString(Y::RANDOM_NONCESTRING, 32);
        }
        return parent::beforeSave($insert);
    }
/*
    public function getClient() {
        return $this->hasOne(WxClient::className(), ['id' => 'client_id']);
    }

    public function getClientName() {
        if (!empty($this->client))
            return $this->client->shortname;
        else
            return '';
    }
*/
    private function postXmlCurl($xml, $url, $useCert = false, $second = 30) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($useCert == true) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, Yii::getAlias("@app/../cert/{$this->gh_id}/apiclient_cert.pem"));
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, Yii::getAlias("@app/../cert/{$this->gh_id}/apiclient_key.pem"));
        }
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WxPayException("curl出错，错误码:$error");
        }
    }

    public function getWxUser($openid, $getinfo = true) {
        $wx_user = WxUser::findOne([
             'openid' => $openid,
        ]);
        
        if (empty($wx_user)) {
            $wx_user = new WxUser([
                'gh_id' => $this->gh_id,
                'openid' => $openid,
            ]);
            $wx_user->save(false);
            $wx_user->refresh();
        }
        
        if ($getinfo && (empty($wx_user->nickname) || $wx_user->updated_at < date("Y-m-d", time() - 24 * 3600))) {
            $wxapp = $this->getWxApp()->getApplication();
            $arrResponse = $wxapp->user->get($openid)->toArray();            
            $wx_user->setAttributes($arrResponse);
            $wx_user->save(false);
            $wx_user->refresh();
        }
        return $wx_user;
    }

    public function getWxApp($scope = 'snsapi_base', $dynamicOauthCallback = true) 
    {
        if  (yii::$app->has('wx')) {
            return  yii::$app->get('wx');        
        }
        Yii::$app->set('wx', [
            'class' => 'common\wosotech\WX',
            'config' => [
                'debug' => true,
                'app_id' => $this->appId,
                'secret' => $this->appSecret,
                'token' => $this->token,
                'aes_key' => $this->encodingAESKey,
                'log' => [
                    'level' => 'debug',
                    'file'  => (yii::$app instanceof yii\console\Application) ? './runtime/easywechat.log' : '../runtime/easywechat.log',
                ],
                'oauth' => [
                    'scopes' => [$scope], // scopes: snsapi_userinfo, snsapi_base, snsapi_login
                    'callback' => $dynamicOauthCallback ? Url::current() : Url::to(['wap/callback']),
                ],
                'payment' => [
                    'merchant_id' => $this->wxPayMchId,                    
                    'key' => $this->wxPayApiKey,            // apikey
                    'cert_path' => Yii::getAlias("@app/../cert/{$this->gh_id}/apiclient_cert.pem"),
                    'key_path' => Yii::getAlias("@app/../cert/{$this->gh_id}/apiclient_key.pem"),       
                    'notify_url'         => 'http://m.buy027.com/wxpaynotify.php',
                    // 'device_info'     => '013467007045764',
                    // 'sub_app_id'      => '',
                    // 'sub_merchant_id' => '',
                    // ...
                ],                         
                'guzzle' => [
                    'timeout' => 5, 
                    'verify'=> false,
                ],                                    
            ]            
        ]);  
        return  yii::$app->get('wx');
    }

    public function getSessionOpenid($dynamicOauthCallback = true, $scope = 'snsapi_base') 
    {
         $wxapp =  $this->getWxApp($scope, $dynamicOauthCallback)->getApplication();         
         $oauth = $wxapp->oauth;
         if (empty(\Yii::$app->request->get('code'))) {
             $oauth->redirect()->send();
             exit;                                  
         }

         $user = $oauth->user();
         $token = $user->getToken()->toArray();        
         $info = $token;
         //$openid = $token['openid'];        
         /*        
         [
             'access_token' => 'xxx',
             'expires_in' => 7200,
             'refresh_token' => 'yyy',
             'openid' => 'oD8xWwg-GJiFi9RLEllEzR1bwJ9A',
             'scope' => 'snsapi_userinfo',
         ]
         */
         
         if ('snsapi_userinfo' == $token['scope']) {
             $info = $originalUser = $user->getOriginal();
             /*        
             [
                 'openid' => 'oD8xWwg-GJiFi9RLEllEzR1bwJ9A',
                 'nickname' => 'xx',
                 'sex' => 1,
                 'language' => 'zh_CN',
                 'city' => 'x',
                 'province' => 'xx',
                 'country' => 'xx',
                 'headimgurl' => 'http://wx.qlogo.cn/mmopen/Uf2Tkt1hetGliaFhJPGqIk23ZyE0Y7AFCmefYQAbic2yNRdjO0ZsepFlWA2CHUcewXsqdGIQ0q5nvCIxVJmkAUFzORhqraI5Mp/0',
                 'privilege' => [],
             ]
             */      
             
         }
                 
        //\Yii::$app->session['openid'] = $info;
        // $wxUser = $this->getWxUser($openid); // ???
        return $info;
    }

    public function setKeyStorage($key, $value)
    {
        return Yii::$app->keyStorage->set("{$this->gh_id}.{$key}", $value);
    }

    public function getKeyStorage($key, $default = null, $cache = true, $cachingDuration = false)
    {
        return Yii::$app->keyStorage->get("{$this->gh_id}.{$key}", $default, $cache, $cachingDuration);
    }

    const PLATFORM_WXP = 0;
    const PLATFORM_HUILONG = 1;
    public static function getPlatformOptionName($key = null)
    {
        $arr = [
            static::PLATFORM_WXP => 'WXP',
            static::PLATFORM_HUILONG => '汇龙',
        ];
        return $key === null ? $arr : (isset($arr[$key]) ? $arr[$key] : '');
    }

    public function getQrImageUrl($width = 200, $height = 200, $thumbnailMode = "outbound") {
        return empty($this->qr_image_id) ? '' : \Yii::$app->imagemanager->getImagePath($this->qr_image_id, $width, $height, $thumbnailMode);
    }

    public function getQyWechat() 
    {
        if  (yii::$app->has('QyWechat')) {
            return  yii::$app->get('QyWechat');        
        }
        $options = [
            'token'=>$this->token,
            'encodingaeskey'=>$this->encodingAESKey,
            'appid'=>$this->appId,
            'appsecret'=>$this->appSecret,
            'agentid'=> $this->gh_id,
            'debug' => false,
            'logcallback'=>'yii::error',
        ];

        Yii::$app->set('QyWechat', new \wechat\models\QyWechat($options));
        
        return  yii::$app->get('QyWechat');        
    }
}

