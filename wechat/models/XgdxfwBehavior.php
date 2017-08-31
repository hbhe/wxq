<?php

namespace wechat\models;

use yii\base\Behavior;
use yii\helpers\Json;

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;

class XgdxfwBehavior extends Behavior {
    public function events() {
        return [
            Wechat::EVENT_MSG_EVENT_SUBSCRIBE => 'welcome',
            //Wechat::EVENT_MSG_TEXT => 'xgdxfwWechat',
            //Wechat::EVENT_MSG_IMAGE => 'xgdxfwWechat',
        ];
    }
    
    public function welcome($event) {
        $owner = $this->owner;
        $message = $owner->message;
        $gh = $owner->gh;
        $wxUser = $owner->wxUser;
        $owner->wxUser->updateAttributes(['subscribe' => 1]);
        $msgType = $owner->message->MsgType;
        $openid = $message->get('FromUserName');
        /* $new = new Text(['content' => "亲，欢迎关注孝感电信公众号！查询办理一键搞定，充值缴费快捷放心，最新优惠抢先体验哦！\r\n回复1---流量包订购\r\n回复2---推荐有礼\r\n回复3---话费充值\r\n回复4---话费查询"]);
        $wxapp = $gh->getWxApp('snsapi_base', false)->getApplication();
        $wxapp->staff->message($new)->to($openid)->send();
        $owner->response = new News(['title' => "亲爱的{$owner->wxUser->nickname}，告诉你一个好消息-推荐有礼，特权福利免费拿啦！",'description'=>"孝感电信，推荐有礼福利来袭！",'url'=>"http://mp.weixin.qq.com/s/LTqtY-q71FV-7p6Jtf4CBQ",'image'=>"http://test.buy027.com/wxp/mobile/web/img/xgdx.jpg"]);
         */
        /* $news1 = new News(['title'  => '第一张图','description'=>'第一张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
        $news2 = new News(['title' => '第二张图','description'=>'第二张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
        $news3 = new News(['title' => '第三张图','description'=>'第三张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
        $news4 = new News(['title' => '第四张图','description'=>'第四张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
        $news5 = new News(['title' => '第五张图','description'=>'第五张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
        $news6 = new News(['title' => '第6张图','description'=>'第6张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
        $news7 = new News(['title' => '第7张图','description'=>'第五张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
        $news8 = new News(['title' => '第8张图','description'=>'第6张图描述','url'=>'www.baidu.com','image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
       
        
        $wxapp = $gh->getWxApp('snsapi_base', false)->getApplication();
        $wxapp->staff->message([$news1,$news2,$news3,$news4,$news5,$news6,$news7,$news8])->to($openid)->send();  */
        //$wxapp = $gh->getWxApp('snsapi_base', false)->getApplication();
        //$wxapp->staff->message([$news])->to($openid)->send();
 //       $owner->response
//        $news = new News(['title' => "亲爱的{$owner->wxUser->nickname}，告诉你一个好消息-推荐有礼，特权福利免费拿啦！",'description'=>"孝感电信，推荐有礼福利来袭！",'url'=>"http://mp.weixin.qq.com/s/LTqtY-q71FV-7p6Jtf4CBQ",'image'=>"http://test.buy027.com/wxp/mobile/web/img/xgdx.jpg"]);
    }
    
    
    /* public function xgdxfwWechat($event) {
        $owner = $this->owner;
        $gh = $owner->gh;
        $wxUser = $owner->wxUser;
        $message = $owner->message;
        $msgType = $message->get('MsgType');         
        
        $owner->response = new Text(['content' => "亲，欢迎关注孝感电信公众号！查询办理一键搞定，充值缴费快捷放心，最新优惠抢先体验哦！"]);                    
    } */
}
