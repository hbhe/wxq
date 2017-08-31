<?php
namespace wechat\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

use common\models\WxGh;
use common\models\WxUser;

use wechat\models\Wechat;
/*
 * http://wxe-wechat.buy027.com/index.php?r=site&appid=wx0b4f26d460868a25&agentid=7 
 * http://127.0.0.1/wxe/wechat/web/index.php?r=site&appid=wx0b4f26d460868a25&agentid=7 
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;    

    public function actionIndex($appid, $agentid) {
        $wechat = new Wechat();
        return $wechat->run($appid, $agentid);
    }    
}
