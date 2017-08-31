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

class XgdxTextBehavior extends Behavior {
    public function events() {
        return [
            Wechat::EVENT_MSG_TEXT => 'xgdxtextWechat',
            Wechat::EVENT_MSG_IMAGE => 'xgdxtextWechat',
 //           Wechat::EVENT_MSG_EVENT_HEADER => 'xgdxtextWechat',
 //           Wechat::EVENT_MSG_EVENT_SCAN => 'xgdxtextWechat',
        ];
    }
    
     public function xgdxtextWechat($event) {
        $owner = $this->owner;
        $gh = $owner->gh;
        $wxUser = $owner->wxUser;
        $message = $owner->message;
        $msgType = $message->get('MsgType');
 //       $event = $message->get('Event');
        $content = $message->get('Content');
        $openid = $message->get('FromUserName');
      /*   if ($msgType == 'event' && $event == 'SCAN') {
            $news = new News(['title' => "亲爱的{$owner->wxUser->nickname}，告诉你一个好消息-推荐有礼，特权福利免费拿啦！",'description'=>"孝感电信，推荐有礼福利来袭！",'url'=>"http://mp.weixin.qq.com/s/LTqtY-q71FV-7p6Jtf4CBQ",'image'=>"http://test.buy027.com/wxp/mobile/web/img/xgdx.jpg"]);
            $wxapp = $gh->getWxApp('snsapi_base', false)->getApplication();
            $wxapp->staff->message([$news])->to($openid)->send();
        } */
        if ($msgType == 'text') {
            if ($content == '推荐有礼' || $content == '推荐' ||$content == '有礼' || $content == '2') {
                $owner->response = new News(['title' => "亲爱的{$owner->wxUser->nickname}，告诉你一个好消息-推荐有礼，特权福利免费拿啦！",'description'=>"孝感电信，推荐有礼福利来袭！",'url'=>"http://mp.weixin.qq.com/s/LTqtY-q71FV-7p6Jtf4CBQ",'image'=>"http://test.buy027.com/wxp/mobile/web/img/xgdx.jpg"]);
            } elseif ($content == '1' || $content == '流量包订购' || $content == '订购' || $content == '流量包') {
                $owner->response = new Text(['content' => "流量加油包\r\nhttp://cmccjz.buy027.com/dxflow/list?type=xgdx"]);
            } elseif ($content == '3' || $content == '话费充值' || $content == '充值') {
                $owner->response = new Text(['content' => "话费充值\r\nhttp://w.02786310000.cn/Wap/?c=Charge&a=index&showwxpaytitle=1&fp=ooejCjlSEdh9ZS99eMFARXcSVEDE&CKTAG=mta_share.wechat_friend"]);
            } elseif ($content == '4' || $content == '话费查询' || $content == '查询') {
                $owner->response = new Text(['content' => "话费查询\r\nhttp://waphb.189.cn/query/zhangdan.shtml"]);
            } elseif ($content == '话费') {
                $owner->response = new Text(['content' => "话费查询\r\nhttp://waphb.189.cn/query/zhangdan.shtml\r\n话费充值\r\nhttp://w.02786310000.cn/Wap/?c=Charge&a=index&showwxpaytitle=1&fp=ooejCjlSEdh9ZS99eMFARXcSVEDE&CKTAG=mta_share.wechat_friend"]);
            } elseif ($content == '11') {
                $wxapp = $gh->getWxApp('snsapi_base', false)->getApplication();
                $qrcode = $wxapp->qrcode;
                $result = $qrcode->forever($openid);// 或者 $qrcode->forever("foo");
                $ticket = $result->ticket;   // 或者 $result['ticket']
                $url = $result->url;
                $owner->response = new Text(['content' => $ticket]);
            } else {
                $owner->response = new Text(['content' =>"回复1---流量包订购\r\n回复2---推荐有礼\r\n回复3---话费充值\r\n回复4---话费查询"]);
            }
        }                        
    } 
}
