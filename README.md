微信企业号平台

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```

安装步骤:
1. 先clone到本地后, 执行composer install (如果很慢，直接copy一份vendor)
2. php init

create database wxq

2. php yii migrate --migrationPath=@noam148/imagemanager/migrations
3. php yii migrate/up   (php yii app/setup)




应用(Agent)前台，即应用主页URL： 
http://wxq-frontend.buy027.com/index.php?r=agent/frontend&agent_sid=ezoa-agent&corpid=$CORPID$&agentid=$AGENTID$
如:

应用(Agent)后台url, 即业务设置URL: 
如: http://wxq-admin.buy027.com/index.php?r=auth/agent-backend&agent_sid=ezoa-agent&corpid=wxe675e8d30802ff44&auth_code=t3ArVy4uetdevIg8PBDl9ilL640sQ-Q6mfbQ6o4a8MCCGH7P5VAugz3SCUCiallsq6C1fvmbZPL3GJAtveWIOQ

应用的消息callback url: http://wxq-admin.buy027.com/index.php?r=auth/agent-callback&agent_sid=ezoa-agent&corpid=$CORPID$