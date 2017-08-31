<?php

namespace common\wosotech\base;

use Yii;
use yii\base\InlineAction;
use yii\helpers\Url;

class Controller extends \yii\web\Controller
{
    public function actionAjaxBroker($args)
    {
        $args = json_decode($args, true);        
        if (YII_ENV_DEV) {
            yii::info(print_r($args, true));
        }
        return call_user_func(array($args['classname'], $args['funcname']), $args['params']);
    }

}
