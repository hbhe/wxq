<?php

namespace wechat\models;

use yii;
use yii\base\Behavior;
use yii\helpers\Json;

/*
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use common\models\WxUser;
use common\models\WxKeyword;
use common\models\WxKeywordSearch;
use yii;
use yii\httpclient\Client;
*/

class WxKeywordBehavior extends Behavior {
    public function events() {
        return [
            Wechat::EVENT_MSG_TEXT => 'wxkeywordWechat',
            Wechat::EVENT_MSG_EVENT_SUBSCRIBE=> 'wxkeywordWechat',
            Wechat::EVENT_MSG_EVENT_CLICK => 'wxkeywordWechat',
        ];
    }
    
     public function wxkeywordWechat($event) {
        $owner = $this->owner;
        $gh = $owner->gh;
        $we = $owner->we;
        $arr = $we->getRevData();        
        $from = $we->getRevFrom();
        $msgType = $we->getRevType();
        $content = $we->getRevContent();
        
        //$owner->response = $we->text("你好," . $from . "\n你发送的" . $msgType . "类型信息：\n原始信息如下：\n" . var_export($arr, true))->reply([], true);
        $owner->response = $we->text("你好," . $from . "\n你发送的" . $msgType . "类型信息：\n原始信息如下：\n" . var_export($arr, true));
    } 

    
}
