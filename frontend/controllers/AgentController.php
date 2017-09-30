<?php
namespace frontend\controllers;

use common\models\Agent;
use common\models\Corp;
use common\models\CorpSuite;
use common\models\Employee;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Site controller
 */
class AgentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 微信用户，在点击应用(Agent)时进入到主页(即Agent前台)
     * http://wxq-frontend.buy027.com/index.php?r=agent/frontend&agent_sid=ezoa-agent&corpid=$CORPID$&agentid=$AGENTID$
     * http://127.0.0.1/wxq/frontend/web/index.php?r=agent/frontend&agent_sid=ezoa-agent&corpid=$CORPID$&agentid=$AGENTID$
     * @return string
     */
    public function actionFrontend()
    {
        /*
        [
            'r' => 'agent/frontend',
            'agent_sid' => 'ezoa-agent',
            'corpid' => 'wxe675e8d30802ff44',
            'agentid' => '1000007',
        ],
        */
        Yii::error([__METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);

        if (empty($corpid = Yii::$app->request->get('corpid'))) {
            Yii::error(['no corpid parameter', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no corpid parameter');
        }

        if (empty($agent_sid = Yii::$app->request->get('agent_sid'))) {
            Yii::error(['no agent_id parameter', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no agent_id parameter');
        }

        if (empty($agentid = Yii::$app->request->get('agentid'))) {
            Yii::error(['no agentid parameter', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no agentid parameter');
        }

        $corp = Corp::findOne(['corp_id' => $corpid]);
        if ($corp === null) {
            Yii::error(['no corp', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no corp');
        }

        $agent = Agent::findOne(['sid' => $agent_sid]);
        if ($agent === null) {
            Yii::error(['no agent', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no agent');
        }

        $suite = $agent->suite;
        if ($suite === null) {
            Yii::error(['no suite', __METHOD__, __LINE__, $agent->toArray(), $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no suite');
        }

        // get mobile
        $model = CorpSuite::findOne(['corp_id' => $corpid, 'suite_id' => $suite->suite_id]);
        $we = $model->getQyWechat();
        if (empty(\Yii::$app->request->get('code'))) {
            //snsapi_userinfo, snsapi_privateinfo
            // 构造链接时appid使用使用者企业的corpid
            $we->setAppid($corpid);
            $url = $we->getOauthRedirect(Url::current([], true), 'STATE', 'snsapi_privateinfo', $agentid);
            //Yii::error([__METHOD__, __LINE__, $url]);
            //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe675e8d30802ff44&redirect_uri=http%3A%2F%2Fwxq-frontend.buy028.com%2Findex.php%3Fr%3Dagent%252Ffrontend%26agent_sid%3Dezoa-agent%26corpid%3Dwxe675e8d30802ff44%26agentid%3D1000010&response_type=code&scope=snsapi_userinfo&agentid=1000010&state=STATE#wechat_redirect
            return $this->redirect($url);
        }

        /*
        [
            'r' => 'agent/frontend'
            'agent_sid' => 'ezoa-agent'
            'corpid' => 'wxe675e8d30802ff44'
            'agentid' => '1000010'
            'code' => 'Q6Hqf5rndg-RUFmgADbqQ1Q0pbZ13xNvoeYb-v1r-ss'
            'state' => 'STATE'
        ]
        */
        $userInfo = $we->getUserId(Yii::$app->request->get('code'));
        /*
        Yii::error($userInfo);
        //snsapi_privateinfo
        [
            'UserId' => 'hhb',
            'DeviceId' => '865124037633551',
            'errcode' => 0,
            'errmsg' => 'ok',
            'user_ticket' => 'DSEP1EgkUaReR0G7KHzv0_7dsHd_rvU7t3OijHn-smu7UKvFRj-aIlPpQoxqiyD4Dix6MkLPPAKcT4v6aRbgCuOChdOVvib9MDH6pBT-EsY',
            'expires_in' => 1800,
        ]
        */

        $userDetail = $we->getUserDetail($userInfo['user_ticket']);
        /*
        Yii::error($userDetail);
        // for snsapi_userinfo
        [
            'errcode' => 0,
            'errmsg' => '',
            'userid' => 'hhb',
            'name' => 'xxx',
            'department' => [
                3,
            ],
            'position' => '',
            'gender' => '1',
            'avatar' => 'http://shp.qpic.cn/bizmp/YI2BzCzzDnauvibjpooLXHaLph9g9D2tcmgEHiaiaIMnqNVib4H4Tn7pKw/',
            'status' => 1,
            'extattr' => [
                'attrs' => [],
            ],
            'order' => [],
            'wxplugin_status' => 1,
        ]

        // for snsapi_privateinfo
        [
            'errcode' => 0,
            'errmsg' => '',
            'userid' => 'hhb',
            'name' => 'xx',
            'department' => [
                3,
            ],
            'position' => '',
            'mobile' => '15527210477',
            'gender' => '1',
            'email' => '',
            'avatar' => 'http://shp.qpic.cn/bizmp/YI2BzCzzDnauvibjpooLXHaLph9g9D2tcmgEHiaiaIMnqNVib4H4Tn7pKw/',
            'status' => 1,
            'extattr' => [
                'attrs' => [],
            ],
            'telephone' => '',
            'order' => [],
            'wxplugin_status' => 1,
        ]
        */
        Employee::findOne([])
        return $this->redirect([$agent_sid]);
    }

    // for agent_sid = demo-agent
    public function actionDemoAgent()
    {
        return $this->route;
    }

    // for agent_sid = ezoa-agent
    public function actionEzoaAgent()
    {
        return $this->route;
    }
}
