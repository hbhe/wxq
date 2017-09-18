<?php
return [
    'name'=>'微信企业号平台',
    'language' => 'zh-CN',	
    'timeZone' => 'Asia/Shanghai',	    
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wxq',
            'username' => 'xxx',
            'password' => 'xxx',
            'charset' => 'utf8',
            'tablePrefix' => 'wxq_',
            'enableSchemaCache' => YII_ENV_PROD,            
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        
        'mutex' => [
            //'class' => 'yii\mutex\FileMutex',
            'class' => 'yii\mutex\MysqlMutex',
        ], 
        
        'imagemanager' => [
            'class' => 'noam148\imagemanager\components\ImageManagerGetPath',
            //set media path (outside the web folder is possible)
            'mediaPath' => Yii::getAlias('@backend/web/img'),
            //path relative web folder to store the cache images
            //'cachePath' => 'assets/images',
            'cachePath' => 'image-cache',
            //use filename (seo friendly) for resized images else use a hash
            'useFilename' => true,
            //show full url (for example in case of a API)
            'absoluteUrl' => false,
        ],    

        'fs' => [
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path' => '@webroot/files1',
        ],

        
/*
        'fs' => [
            'class' => 'creocoder\flysystem\SftpFilesystem',
            'host' => 'www.buy027.com',
            // 'port' => 22,
            'username' => 'root',
            'password' => 'r9o8o7t6',
            //'privateKey' => '/path/to/or/contents/of/privatekey',
            // 'timeout' => 60,
            // 'root' => '/path/to/root',
            // 'permPrivate' => 0700,
            // 'permPublic' => 0744,
        ],
*/

/*
        'fs' => [
            'class' => 'yii2tech\filestorage\local\Storage',
            'basePath' => '@webroot/files',
            'baseUrl' => '@web/files',
            'filePermission' => 0777,
            'buckets' => [
                'tempFiles' => [
                    'baseSubPath' => 'temp',
                    'fileSubDirTemplate' => '{^name}/{^^name}',
                ],
                'imageFiles' => [
                    'baseSubPath' => 'image',
                    'fileSubDirTemplate' => '{ext}/{^name}/{^^name}',
                ],
            ]
        ],
*/        

        'fileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@web/files1',
            'filesystemComponent'=> 'fs'
        ],
        
    ],

    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'templateFile' => '@jamband/schemadump/template.php',
        ],
        'schemadump' => [
            'class' => jamband\schemadump\SchemaDumpController::class,
            'db' => [
                'class' => yii\db\Connection::class,
                'dsn' => 'mysql:host=localhost;dbname=oc2',
                'username' => 'root',
                'password' => '',
            ],
        ],
    ],

    'modules' => [
/*    
        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['57620133','hehbhehb'], 
            'enableConfirmation' => false,
            //'enableUnconfirmedLogin' => true,
            
            'controllerMap' => [
                'admin' => [
                    'class'  => 'dektrium\user\controllers\AdminController',
                    //'layout' => 'path-to-your-admin-layout',
                ],

                'registration' => [
                    'class'  => 'dektrium\user\controllers\RegistrationController',
                    'layout' => '@app/views/layouts/main-login-dektrium.php',
                ],

                'security' => [
                    'class'  => 'dektrium\user\controllers\SecurityController',
                    'layout' => '@app/views/layouts/main-login-dektrium.php',
                ],
                
            ],            
        ],
*/     

        'imagemanager' => [
            'class' => 'noam148\imagemanager\Module',
            //set accces rules ()
            'canUploadImage' => true,
            'canRemoveImage' => function(){
                return true;
            },
            // Set if blameable behavior is used, if it is, callable function can also be used
            'setBlameableBehavior' => false,
            //add css files (to use in media manage selector iframe)
            'cssFiles' => [
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css',
            ],
        ],
    ],
];
