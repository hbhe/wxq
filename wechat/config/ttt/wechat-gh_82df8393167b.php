<?php
return [
    'behaviors' => [
            wechat\models\XgdxfwBehavior::className(),
            wechat\models\XgdxTextBehavior::className(),
            wechat\models\XgMemberBehavior::className(),
            wechat\models\WxCeshiBehavior::className(),
    ],    
];

