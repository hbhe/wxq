<?php
return [
    'behaviors' => [
        wechat\models\LoggingBehavior::className(),
        wechat\models\WxUserBehavior::className(),
        
        wechat\models\SubscribeBehavior::className(),
        wechat\models\UnsubscribeBehavior::className(),
        wechat\models\WxKeywordBehavior::className(),
                
        //wechat\models\TextBehavior::className(),        
        //wechat\models\DefaultBehavior::className(),

    ],    
];

