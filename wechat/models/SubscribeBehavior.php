<?php

namespace wechat\models;

use yii\base\Behavior;
use yii\helpers\Url;

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;

class SubscribeBehavior extends Behavior {
    public function events() {
        return [
            Wechat::EVENT_MSG_EVENT_SUBSCRIBE => 'welcome',
        ];
    }
    
    public function welcome($event) {
        $owner = $this->owner;
        $owner->wxUser->updateAttributes(['subscribe' => 1]);
        $msgType = $owner->message->MsgType;  
        $owner->response = new Text(['content' => "{$owner->wxUser->nickname}，欢迎关注{$owner->gh->title}!"]);                    
    }
}
