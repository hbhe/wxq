<?php
return [
    'behaviors' => [
        wechat\models\WxWallBehavior::className(),
        wechat\models\WxWallSignBehavior::className(),
        wechat\models\WxWallShakeBehavior::className(),
        //wechat\models\WxCeshiBehavior::className(),

        //wechat\models\TransferBehavior::className(),
    ],    
];

