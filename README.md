微信企业号平台


安装步骤:
1. 先clone到本地后, 执行composer install (如果很慢，直接copy一份vendor)
2. php init

create database wxq

2. php yii migrate --migrationPath=@noam148/imagemanager/migrations
3. php yii migrate/up





如何创建套件
0. 修改common/config/params.php, 将corp_id设置成套件开发商自己的corpid
    'corp_id' => 'wx0b4f26d460868111', 
1. 在wxq后台创建suite, 注意要输入suite_sid（建议选一个例于记忆的字符串,如ezoa）, Token(随便输入如1111), EncodingAESKey（输入43位长的随机字符串如1234567890123456789012345678901234567890123）， SuiteID, SuiteSecret 先空着不用填, 点创建    
2. 在企业号服务商管理后台->应用套件->创建应用套件，输入
    发起安装域名: wxq-admin.buy027.com
    安装完成回调域名: wxq-admin.buy027.com
    系统事件接收URL: http://wxq-admin.buy027.com/index.php?r=auth&suite_sid=ezoa     (suite_sid,Token,EncodingAESKey的值取第1步输入的值) 
    Token: 1111
    EncodingAESKey: 1234567890123456789012345678901234567890123
    IP白名单: 服务器ip地址 
3. 回到wxq后台，编辑suite, 将第2步官网上生成的SuiteID(套件ID), SuiteSecret回填到suite中, 点更新

如何在套件内创建应用(Agent)?
1. 在wxq后台，创建一个agent, 输入sid(选一个例于记忆的字符串,如ezoa-agent), 下拉选一个套件，表示它属于哪个套件下的, 点创建
2. 在应用套件详情内，点"创建应用", 填写logo, 重点是
    CallbackURL: http://wxq-admin.buy027.com/index.php?r=agent/callback&agent_sid=ezoa-agent&corpid=$CORPID$ 
    业务设置URL: http://wxq-admin.buy027.com/index.php?r=agent/backend&agent_sid=ezoa-agent&corpid=$CORPID$
    可信域名(用于网页授权及JS-SDK): wxq-frontend.buy027.com
    应用主页: http://wxq-frontend.buy027.com/index.php?r=agent/frontend&agent_sid=ezoa-agent&corpid=$CORPID$&agentid=$AGENTID$
    
    注意:其中ezoa-agent参数是在第1步时输入的字符串

如何测试agent
1. 在应用套件详情内，点"测试安装"，选企业A
    在企业A的后台, 在企业应用内，点“前往服务商后台”，能看到后台设置页面
    给某用户发一条消息，callback能收到消息
    用户在手机微信企业号上，点应用，能打开“应用主页”页面
测试OK               

企业号官网服务商在创建套件和应用时相关URL填写办法

Suite消息处理URL
* http://wxq-admin.buy027.com/index.php?r=auth&suite_sid=ezoa

Agent的消息callback
* 当微信用户发消息时, 每个应用(Agent)都要设置一个处理url来处理消息, 支持$CORPID$模板变量,
* 不过url参数中corpid有时是服务商的corpid(当有echostr参数时)，有时又是使用者企业corpid(与ToUserName相同)
* http://wxq-admin.buy027.com/index.php?r=agent/callback&agent_sid=ezoa-agent&corpid=$CORPID$

Agent前台，即应用主页URL： 
* 微信用户，在点击应用(Agent)时进入到主页(即Agent前台)
* http://wxq-frontend.buy027.com/index.php?r=agent/frontend&agent_sid=ezoa-agent&corpid=$CORPID$&agentid=$AGENTID$

Agent后台url, 即业务设置URL: 
* Agent业务设置(Agent管理后台)URL, 支持$CORPID$变量
* http://wxq-admin.buy027.com/index.php?r=agent/backend&agent_sid=ezoa-agent&corpid=$CORPID$
*
* 实际会变成：http://wxq-admin.buy027.com/index.php?r=agent/backend&agent_sid=ezoa-agent&corpid=wxe675e8d30802ff44&auth_code=t3ArVy4uetdevIg8PBDl9ilL640sQ-Q6mfbQ6o4a8MD6gPu0v55fftS1H0csGmsP6cov69Bd5QV7UuL_PHTxKevZRGTUtQ6QKyfwGVELFxM


