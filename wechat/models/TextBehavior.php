<?php

namespace wechat\models;

use yii\base\Behavior;
use yii\helpers\Json;
use common\models\WxtppConfig;

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;


class TextBehavior extends Behavior {
    public function events() {
        return [
            Wechat::EVENT_MSG_TEXT => 'keywordAutoreply',
            Wechat::EVENT_MSG_EVENT_CLICK => 'keywordAutoreply',
        ];
    }
    
    public function keywordAutoreply($event) {
        $owner = $this->owner;
        if ('text' == $owner->getRequest('MsgType'))
            $text = $owner->getRequest('Content');
        else
            $text = $owner->getRequest('EventKey');
        $autoreply = WxtppConfig::getConfig($owner->getRequest('ToUserName'), WxtppConfig::KEY_AUTOREPLY_KEYWORD_HEADER . $text);
        if (!empty($autoreply)) {
            $owner->response['MsgType'] = 'text';
            $owner->response['FromUserName'] = $owner->getRequest('ToUserName');
            $owner->response['ToUserName'] = $owner->getRequest('FromUserName');
            $owner->response['CreateTime'] = time();
            $autoreply = str_replace('{{nickname}}', $owner->wxUser->nickname, $autoreply);
            $autoreply = str_replace('{{ghtitle}}', $owner->gh->title, $autoreply);
            $autoreply = str_replace('{{hyzxurl}}', "http://m.wxtpp.wosotech.com/site/hyzx?gh_id={$owner->wxUser->gh_id}&openid={$owner->wxUser->openid}", $autoreply); 
            $owner->response['Content'] = $autoreply;
        } 
//        else {
//            $owner->response['MsgType'] = 'transfer_customer_service';
//            $owner->response['FromUserName'] = $owner->getRequest('ToUserName');
//            $owner->response['ToUserName'] = $owner->getRequest('FromUserName');
//            $owner->response['CreateTime'] = time();
//        }
    }
}
