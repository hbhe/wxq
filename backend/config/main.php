<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logVars' => ['_POST','_GET'],
                    //'levels' => ['error', 'warning'],
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                ],
            ],
        ],
        
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */

        /*
        'view' => [
             'theme' => [
                 'pathMap' => [
                    '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
                    //'@app/views' => '@app/adminlte'
                 ],
             ],
        ],
        */

        'assetManager' => [
            'appendTimestamp' => YII_ENV_DEV,
            'assetMap' => [
                'jquery.js' => '//cdn.bootcss.com/jquery/2.2.4/jquery.min.js',
                'bootstrap.css' => '//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css',
                'bootstrap.js' => '//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js',
                'jquery-ui.css' => '//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.min.css',
                'jquery-ui.js' => '//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.min.js',                                
            ],
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                    /*
                    //'skin' => 'skin-green', 
                    'css' => [
                        '@backendUrl/css/AdminLTE.min.css',
                    ],
                    
                    'depends' => [
                        'rmrevin\yii\fontawesome\cdn\AssetBundle'
                    ],  
                    */
                ],
            ],
        ],

    ],
    
    'params' => $params,
];
