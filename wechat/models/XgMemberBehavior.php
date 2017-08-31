<?php

namespace wechat\models;

use yii\base\Behavior;
use yii\helpers\Json;

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use common\models\WxUser;
use common\models\WxXgdxMember;
use common\models\WxXgdxMemberSearch;
use common\models\WxXgMemberfans;
use yii;
use common\models\WxQrlimit;

class XgMemberBehavior extends Behavior {
    public function events() {
        return [
            Wechat::EVENT_MSG_EVENT_SUBSCRIBE=> 'xgmemberWechat',
        ];
    }
    
     public function xgmemberWechat($event) {
        $owner = $this->owner;
        $gh = $owner->gh;
        $wxUser = $owner->wxUser;
        $message = $owner->message;
        $msgType = $message->get('MsgType');
        $content = $message->get('Content');
        $openid = $message->get('FromUserName');
        $eventkey=$message->get('EventKey');
        $gh_id=$message->get('ToUserName');
        $event=$message->get('Event');
        if ($msgType == 'event' && $event == 'subscribe' && $eventkey != null) {
            $user=WxXgMemberfans::findOne(['gh_id'=>$gh_id,'openid'=>$openid]);
            if (null !== $user) {
                return;
            }
            
            $scene_str=substr($eventkey, 8);
            $qrlimit = WxQrlimit::findOne(['gh_id'=>$gh_id,'scene_str'=>$scene_str]);
            if ($qrlimit == null) {
                return;
            }
            $to_openid=$qrlimit->action_name;
            $xgdx=WxXgdxMember::findOne(['openid'=>$to_openid,'gh_id'=>$gh_id]);
            $xgdx->vermicelli += 1;
            $xgdx->save();
            
            $xgfans=new WxXgMemberfans();
            $xgfans->gh_id=$gh_id;
            $xgfans->openid=$openid;
            $xgfans->scene_str=$scene_str;
            $xgfans->save();
        }                         
    } 
}
