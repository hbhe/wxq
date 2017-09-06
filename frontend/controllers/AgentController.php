<?php
namespace frontend\controllers;

use common\models\Agent;
use common\models\Corp;
use Yii;
use yii\base\Exception;
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

        return $this->render('index');
    }

}
