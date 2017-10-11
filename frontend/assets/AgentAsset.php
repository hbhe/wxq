<?php

namespace frontend\assets;

use \yii\web\AssetBundle;

class AgentAsset extends AssetBundle
{    
    public $sourcePath = '@frontend/assets/agent';

    public $css = [
        'css/agent.css',
    ];

    public $js = [
        //'js/app.js',
    ];

    public $depends = [
//        'yii\web\JqueryAsset',
        'common\wosotech\WeuiJSAsset',
    ];
}
