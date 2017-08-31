<?php
namespace wechat\models;

use Yii;
use yii\base\Behavior;
use yii\helpers\Json;

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;

use common\modules\outlet\models\Outlet;


class WxCeshiBehavior extends Behavior {
    public function events() {
        return [
            Wechat::EVENT_MSG_TEXT => 'ceshi',
            Wechat::EVENT_MSG_EVENT_LOCATION => 'ceshi',
            Wechat::EVENT_MSG_LOCATION => 'ceshi',
        ];
    }
    
    public function ceshi($event) {
        
        $owner = $this->owner;
        $gh = $owner->gh;
        $wxUser = $owner->wxUser;
        $message = $owner->message;
        $msgType = $message->get('MsgType');
        $content = $message->get('Content');
        $gh_id = $message->get('ToUserName');
        $openid = $message->get('FromUserName');
        //$latitude = $message->get('Latitude');//纬度
        //$longitude = $message->get('Longitude');//经度
        //$precision = $message->get('Precision');//精度
        $lat = $message->get('Location_X');
        $lon = $message->get('Location_Y');
        //$model = Outlet::find()->all();
        //$label = $message->get('Label');
       /*  if( $msgType == 'location') {
            $rows = Outlet::getNearestOffices($gh_id, $lon, $lat);
            $news = array();
            $i = 0;
            foreach ($rows as $row) {
                $i++;
                if ($i <= 8) {
                    $news[] = new News(['title' => '第'.$i.'张图','description'=>'第'.$i.'张图描述','url'=>'http://sj.buy027.com/index.php?r=outlet/default/index&gh_id='.$gh_id.'&id='.$row['id'].'&lon='.$lon.'&lat='.$lat,'image'=>'http://admin.buy027.com/image-cache/c7/c7e2c4_1490153860-1-.jpg']);
                } else {
                    break;
                }
            }
            $wxapp = $gh->getWxApp('snsapi_base', false)->getApplication();
            $wxapp->staff->message($news)->to($openid)->send();  
        } */
        
    }

}
