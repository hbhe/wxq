<?php
/*
 * http://wxe-wechat.buy027.com/index.php?r=site&appid=wx0b4f26d460868a25&agentid=7 
 * http://127.0.0.1/wxe/wechat/web/index.php?r=site&appid=wx0b4f26d460868a25&agentid=7 
 */
 
namespace wechat\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

use common\models\WxGh;
use wechat\models\QyWechat;

/*
use common\models\WxGh;
use common\models\WxUser;

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
*/

class Wechat extends \yii\base\Component
{
    const RESP_BLANK = '';
    const RESP_SUCCESS = 'success';
    
    const EVENT_BEFORE_PROCESS = 'wechat.msg.beforeprocess';
    const EVENT_AFTER_PROCESS = 'wechat.msg.afterprocess';
    
    const EVENT_MSG_HEADER = 'wechat.msg.';
    const EVENT_MSG_TEXT = 'wechat.msg.text';
    const EVENT_MSG_IMAGE = 'wechat.msg.image';
    const EVENT_MSG_VOICE = 'wechat.msg.voice';
    const EVENT_MSG_VIDEO = 'wechat.msg.video';
    const EVENT_MSG_SHORTVIDEO = 'wechat.msg.shortvideo';
    const EVENT_MSG_LOCATION = 'wechat.msg.location';
    const EVENT_MSG_LINK = 'wechat.msg.link';
    
    const EVENT_MSG_EVENT_HEADER = 'wechat.msg.event.';
    const EVENT_MSG_EVENT_SUBSCRIBE = 'wechat.msg.event.subscribe';
    const EVENT_MSG_EVENT_UNSUBSCRIBE = 'wechat.msg.event.unsubscribe';
    const EVENT_MSG_EVENT_SCAN = 'wechat.msg.event.SCAN';
    const EVENT_MSG_EVENT_LOCATION = 'wechat.msg.event.LOCATION';
    const EVENT_MSG_EVENT_CLICK = 'wechat.msg.event.CLICK';
    const EVENT_MSG_EVENT_VIEW = 'wechat.msg.event.VIEW';
    
    const EVENT_MSG_EVENT_SHAKEAROUNDUSERSHAKE = 'wechat.msg.event.ShakearoundUserShake';
    
    const EVENT_MSG_EVENT_SCANCODE_PUSH = 'wechat.msg.event.scancode_push';
    const EVENT_MSG_EVENT_SCANCODE_WAITMSG = 'wechat.msg.event.scancode_waitmsg';
    const EVENT_MSG_EVENT_PIC_SYSPHOTO = 'wechat.msg.event.pic_sysphoto';
    const EVENT_MSG_EVENT_PIC_PHOTO_OR_ALBUM = 'wechat.msg.event.pic_photo_or_album';
    const EVENT_MSG_EVENT_PIC_WEIXIN = 'wechat.msg.event.pic_weixin';
    const EVENT_MSG_EVENT_LOCATION_SELECT = 'wechat.msg.event.location_select';
    
    const EVENT_MSG_EVENT_CARD_PASS_CHECK = 'wechat.msg.event.card_pass_check';
    const EVENT_MSG_EVENT_CARD_NOT_PASS_CHECK = 'wechat.msg.event.card_not_pass_check';
    const EVENT_MSG_EVENT_USER_GET_CARD = 'wechat.msg.event.user_get_card';
    const EVENT_MSG_EVENT_USER_DEL_CARD = 'wechat.msg.event.user_del_card';
    const EVENT_MSG_EVENT_USER_CONSUME_CARD = 'wechat.msg.event.user_consume_card';
    const EVENT_MSG_EVENT_USER_PAY_FROM_PAY_CELL = 'wechat.msg.event.user_pay_from_pay_cell';
    const EVENT_MSG_EVENT_USER_VIEW_CARD = 'wechat.msg.event.user_view_card';
    const EVENT_MSG_EVENT_USER_ENTER_SESSION_FROM_CARD = 'wechat.msg.event.user_enter_session_from_card';
    const EVENT_MSG_EVENT_UPDATE_MEMBER_CARD = 'wechat.msg.event.update_member_card';
    const EVENT_MSG_EVENT_CARD_SKU_REMIND = 'wechat.msg.event.card_sku_remind';
    
    const EVENT_MSG_EVENT_POI_CHECK_NOTIFY = 'wechat.msg.event.poi_check_notify';

    public $gh;
    
    public $wxUser;

    public $wxapp;    

    public $elapsetime;

    public $message;
    
    public $response;

    public $we;
    
    public function run($appid, $agentid) 
    {
        yii::error([$_GET, $_POST, file_get_contents("php://input")]);        
    
        $this->gh = WxGh::findOne(['appId' => $appid, 'gh_id' => $agentid]);   
        $options = [
            'token'=>$this->gh->token,
            'encodingaeskey'=>$this->gh->encodingAESKey,
            'appid'=>$this->gh->appId,
            'appsecret'=>$this->gh->appSecret,
            'agentid'=> $this->gh->gh_id,
            'debug' => false,
            'logcallback'=>'yii::error',
        ];

        $this->we = new QyWechat($options);

        if (0) {    // just for test
            $this->we->setRevData([
                'ToUserName' => 'ww1de94f786c71be9f',
                'FromUserName' => 'Q309272932',
                'CreateTime' => '1491713871',
                'MsgType' => 'text',
                'Content' => 'ab',
                'MsgId' => '7808921113086957191',
                'AgentID' => '1000002',            
            ]);
        } else {
            if (!$this->we->valid()) {
                yii::error('verify or decrypt failed.');
                yii::$app->end();
            }            
            //yii::error(['decrypted XML', $this->we->getRevPostXml()]);   
            
            $this->we->getRev();            
            yii::error(['data', $this->we->getRevData()]);       
        }

        $response = $this->messageHandle();
        return $response;
    }

    public function messageHandle()
    {
        $data = $this->we->getRevData();        
        $msgType = $this->we->getRevType();    
        
        $this->attachBehaviors($this->gh->gh_id);

        try {        
            $time_start = microtime(true);      

            $this->trigger(self::EVENT_BEFORE_PROCESS);
            if ('event' !== $msgType) {
                $this->trigger(self::EVENT_MSG_HEADER . $msgType);
            } else {
                $event = $message->get('Event');
                $this->trigger(self::EVENT_MSG_EVENT_HEADER . $event);
            }
            
            $time_end = microtime(true);
            $this->elapsetime = $time_end - $time_start;
            $this->trigger(self::EVENT_AFTER_PROCESS);
            
        } catch(\Exception $e) {
            if (YII_DEBUG) {
                Yii::error($e);
            }
            return self::RESP_SUCCESS;
        }
/*        
        if (!empty($this->response)) {
            return $this->response;             
        } else {
            return self::RESP_SUCCESS;
        }
        return $this->response;
*/
        $encryptedResponse = $this->we->reply([], true);
        return $encryptedResponse;
        
    }

    public function attachBehaviors($gh_id) {
        if (file_exists(__DIR__ . "/../config/wechat-{$gh_id}.php")) {
            $config = ArrayHelper::merge(
                require(__DIR__ . '/../config/wechat-default.php'),
                require(__DIR__ . "/../config/wechat-{$gh_id}.php")
            );
        } else {
            $config = require(__DIR__ . '/../config/wechat-default.php');
        }
        if (!empty($config['behaviors'])) {
            parent::attachBehaviors($config['behaviors']);
        }
    }

}

/*
[
    'r' => 'site',
    'agentid' => '7',
    'msg_signature' => '1317e97746202203a672632605818f4f90ba92a5',
    'timestamp' => '1491537209',
    'nonce' => '401148380',
],
[],
'<xml><ToUserName><![CDATA[wx0b4f26d460868a25]]></ToUserName>
<Encrypt><![CDATA[UPosRbcefTf3tUnbrIeTWmazyqlpG9/HTw/SmjM/SULfQpIgsU3m190MdIbXRQBAmXw2TJBkIDvNsIgJnU9pfVGhRF0xlDCVA14vYAlOfGoNoT8uix5i+GsSzam8RWiFlaj0Uxzyma8Y8lK88K3qVkzdAKTcrfH0vvtCC1P9Izax89rfv/9qZzaIksElUbjVnsdh69GhdIw3Mw5gUSiRAEroc42UHEJgOM0ZuMpJPIKq+wy92duxZ34R1pAbn6WXeAgBpdLbi9Qurfm2AtpuxHLAh5aowslSszvk7aNxH0l9IXZP/TCcDJC5vetWiZOIJX5OJEx+zyhGfF8YRin9aH29Yan6NOLVtKCMGdHJTloLVWH9qoikc6NEldY0jLr55a1X4FYTnPa55v9P6YiTk32O81oaFRFcfm7CvczAloo=]]></Encrypt>
<AgentID><![CDATA[7]]></AgentID>
</xml>',
]

<xml><ToUserName><![CDATA[wx0b4f26d460868a25]]></ToUserName>
<FromUserName><![CDATA[hbhe]]></FromUserName>
<CreateTime>1491713871</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[a]]></Content>
<MsgId>7808921113086957191</MsgId>
<AgentID>7</AgentID>
</xml>
[
    'ToUserName' => 'wx0b4f26d460868a25',
    'FromUserName' => 'hbhe',
    'CreateTime' => '1491713871',
    'MsgType' => 'text',
    'Content' => 'a',
    'MsgId' => '7808921113086957191',
    'AgentID' => '7',
]        
*/


