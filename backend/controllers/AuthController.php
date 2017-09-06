<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

use common\models\Corp;
use common\models\Suite;
use common\models\Agent;
use common\models\CorpSuite;
use common\models\CorpAgent;
/**
 * Site controller
 */
class AuthController extends Controller
{
    /**
     * @var bool
     */
    public $enableCsrfValidation = false;
    
    /*
    [
        'r' => 'auth',
        'msg_signature' => 'df39c7b1f7faa629b95bc0a6ca346002e3ce8a65',
        'timestamp' => '1492585060',
        'nonce' => '938321678',
        'echostr' => 'pxf6EB5S6EUI4AGyJ+u4WvvQQwDV4yx7KSTmpiwYU3okE5Rm4p/h9PLtSyOZWwOVTWGr7oUJvZFpupeIws5rJg==',
    ],

    <xml><ToUserName><![CDATA[tj4a1744a4a878638f]]></ToUserName>
    <Encrypt><![CDATA[lY7+o/dRNf+ZxSq5S27Vujur9E9pf8C0leeNJ6A2VdZSuUXeRzFeaKw55Nj37K7W1SBHzAHpCFGUfeENmMqb06SBjL8RyBIVLbjFU/+YzHjEgQSCj5Hl2coIQwIo7Y3dAyuzHhxvqLcmqfhxqrWNRfvtjOALxjUMigrJ55hfCHUvkgksh6O229OdiApLks/fX0WBB1nlIxtUyvuq9Bw0XlO51gO/pY7lw58CQXXGkFl6yYfzkauAGuhsgQDyjwN/yTvIbPBBA68aWeRg4DFY5667ZVlrCP1DOr0JDy0gX0rGh4kA/RNrUBx0NnlocKIL9z5JLcnF6KgtyDnnnF17TrNvroY/MiEnMapGEjsqv17hW7G7fgbDHq4BmgL1hQJV]]></Encrypt>
    <AgentID><![CDATA[]]></AgentID>
    </xml>
    */
    /**
     * 系统事件接收URL, 套件在授权发生变化时，会推送一些事件过来，以下url就是用来处理这个事
     *
     * http://wxq-admin.buy027.com/index.php?r=auth&suite_sid=ezoa
     * http://127.0.0.1/wxe/backend/web/index.php?r=auth&sid=cys_meetings&msg_signature=15b8fda4ed73960ba24dfb5ac2a1f3f8393600a6&timestamp=1492671419&nonce=850142663&echostr=1dWk%2BX164z8hF9XumK2AfWZXZxs21uhZh44PFo44jLATqidO03fBov6TDiXqs4coOwL7Wqm4WdwaMZVThvpf6g%3D%3D
     * http://wxe-admin.buy027.com/index.php?r=auth&sid=cys_worktime
     * http://wxe-admin.buy027.com/index.php?r=auth&sid=cys_meetings
     *
     * @param $suite_sid
     * @return string
     */
    public function actionIndex($suite_sid)
    {
        yii::error([$_GET, $_POST, file_get_contents("php://input")]);                

        $suite = Suite::findOne(['sid' => $suite_sid]);       
        if (null === $suite) {
            yii::error([$_GET, $_POST, file_get_contents("php://input")]);      
            return 'err';
        }
        $we = $suite->getQyWechat();
        if (!$we->valid()) {
            yii::error(['decrypt failed.', $_GET, $_POST, file_get_contents("php://input"), $options]);
            yii::$app->end();
        }  

        $we->getRev();            
        $data = $we->getRevData();
        yii::error(['data', $data]);    
        /*
        yii::error(['data', $data]);            
        [
            'SuiteId' => 'tj8c2445c93840db09',
            'SuiteTicket' => 'J6zl8J5U_tF6Tq6BHI72hdn8Poo7-LIli9p7peAPtr-pVIfsEoAdQRIz3rg-WR_W',
            'InfoType' => 'suite_ticket',
            'TimeStamp' => '1492741808',
            'AgentID' => '',
        ],     
        [
            'SuiteId' => 'tj8c2445c93840db09',
            'AuthCorpId' => 'wxe675e8d30802ff44',
            'InfoType' => 'cancel_auth',
            'TimeStamp' => '1493363816',
            'AgentID' => '',
        ],        
        [
            'SuiteId' => 'tj8c2445c93840db09',
            'Seq' => '2',
            'InfoType' => 'contact_sync',
            'TimeStamp' => '1493365335',
            'AuthCorpId' => 'wxe675e8d30802ff44',
            'AgentID' => '',
        ],     
        [
            'SuiteId' => 'tj8c2445c93840db09',
            'AuthCode' => 'd8QsSuc2dIjEVZLfONXdGGLeN2ihnQ0RsnENtAgRNrA38IKmjfKv709cWTdQr5_5',
            'InfoType' => 'create_auth',
            'TimeStamp' => '1493711908',
            'AgentID' => '',
        ]        
        */
        if ('suite_ticket' == $data['InfoType']) {
            $suite->suite_ticket =  $data['SuiteTicket'];
        } else if ('create_auth' == $data['InfoType']) {
            $arr = $we->getPermanentCode($data['AuthCode']);
            $auth_corp_info = $arr['auth_corp_info'];
            $corp_id = $auth_corp_info['corpid'];

            // Save Corp
            $model = Corp::findOne(['corp_id' => $corp_id]);
            if (null === $model) {
                $model = new Corp();
                $model->corp_id = $corp_id;
            }
            $model->setAttributes($auth_corp_info);
            if (!$model->save(false)) {
                yii::error(['save Corp err', __METHOD__, __LINE__, $model->toArray(), $model->getErrors()]);                
                yii::$app->end();
            }

            // Save CorpSuite
            $model = CorpSuite::findOne(['corp_id' => $corp_id, 'suite_id' => $suite->suite_id]);
            if (null === $model) {
                $model = new CorpSuite();
            }
            $model->corp_id = $corp_id;
            $model->suite_id = $suite->suite_id;    
            $model->permanent_code = $arr['permanent_code'];

            $model->setAttributes($auth_corp_info);
            if (!$model->save(false)) {
                yii::error(['save CorpSuite err', __METHOD__, __LINE__, $model->toArray(), $model->getErrors()]);
                yii::$app->end();
            }

            // test
            $accessToken = $model->getSuiteAccessToken();
            
            
        } else if ('change_auth' == $data['InfoType']) {
            $authCorpId = $data['AuthCorpId'];
        } else if ('cancel_auth' == $data['InfoType']) {
            $authCorpId = $data['AuthCorpId'];   
            $SuiteId = $data['SuiteId']; 
            $model = CorpSuite::findOne(['corp_id' => $authCorpId, 'suite_id' => $SuiteId]);
            if (null !== $model) {
                $model->delete();
            }            

        } else if ('contact_sync' == $data['InfoType']) {
            $authCorpId = $data['AuthCorpId'];   
            $SuiteId = $data['SuiteId']; 
            $model = CorpSuite::findOne(['corp_id' => $corp_id, 'suite_id' => $SuiteId]);
            //$accessToken = $model->getAccessToken();
            
        }
        
        if (!$suite->save()) {
            yii::error([__METHOD__, __LINE__, $suite->getErrors()]);
        }
        
        return 'success';
    }

    /**
     * 每个Agent都有自己的: CallbackURL, 业务设置URL, 可信域名, 应用主页
     *
     * 当微信用户发消息时, 每个应用(agent)都要设置一个处理url, 支持$CORPID$模板变量
     * http://wxe-admin.buy027.com/index.php?r=auth/callback&agent_sid=cys_meeting_agent_meeting&corpid=$CORPID$
     * @param $agent_sid
     * @return string
     */
    public function actionCallback($agent_sid)
    {
        /*
        [
            'r' => 'auth/callback',
            'corpid' => 'wx0b4f26d460868a25',
            'msg_signature' => 'cba9794f8f87a7abb9df1f19f94d6da0afdacdce',
            'timestamp' => '1492662188',
            'nonce' => '1417942269',
            'echostr' => 'HEFucIQmdSR7RtbIaFi6RhEAClGcCSNaCPzZORXGwIsSd0EPBd0EJNGX0inA3dddc8bmHWeTZm7dUThw/G6ReA==',
        ],
        [
            'ToUserName' => 'wxe675e8d30802ff44',
            'FromUserName' => 'maxcvw',
            'CreateTime' => '1493367392',
            'MsgType' => 'event',
            'AgentID' => '3',
            'Event' => 'subscribe',
            'EventKey' => '',
        ],        
        */
        yii::error([$_GET, $_POST, file_get_contents("php://input")]);         

        $agent = Agent::findOne(['sid' => $agent_sid]);       
        if (null === $agent) {
            yii::error(['invalid agent', $_GET, $_POST, file_get_contents("php://input")]);
            yii::$app->end();
        }
        
        $suite = $agent->suite;
        if (!$suite) {
            yii::error(['invalid suite', $_GET, $_POST, file_get_contents("php://input")]);
            yii::$app->end();
        }
        
        $we = $suite->getQyWechat();
        if (!$we->valid()) {
            yii::error(['decrypt failed.', $_GET, $_POST, file_get_contents("php://input"), $options]);
            yii::$app->end();
        }  

        $we->getRev();            
        $data = $we->getRevData();
        yii::error(['data', $data]);    
        if (isset($data['Event']) && 'subscribe' == $data['Event']) {        
            $model = CorpAgent::findOne(['corp_id' => $data['ToUserName'], 'agent_id' => $agent->id]);
            if (null === $model) {
                $model = new CorpAgent();
                $model->corp_id = $data['ToUserName'];
                $model->agent_id = $agent->id;
                if (!$model->save()) {
                    yii::error(['save CorpAgent err', __METHOD__, __LINE__, $model->getErrors()]);                        
                } 
                else yii::error('save CorpAgent ok' . $agent->id);
            } 

            $model = CorpSuite::findOne(['corp_id' => $data['ToUserName'], 'suite_id' => $suite->suite_id]);
            if (null === $model) {
                $model = new CorpSuite();
                $model->corp_id = $data['ToUserName'];
                $model->suite_id = $suite->suite_id;
                if (!$model->save()) {
                    yii::error(['save CorpSuite err', __METHOD__, __LINE__, $model->getErrors()]);                        
                }
            }
            
        }

        return 'success';
    }

    /**
     * Agent级的, 业务设置URL, 好象不支持$CORPID$变量的
     *
     * http://wxe-admin.buy027.com/index.php?r=auth/business&agent_sid=cys_meeting_agent_meeting&corpid=$CORPID$&auth_code=1358d97022b2f8d1f3b38ff273636ea8
     * @param $agent_sid
     * @return string
     */
    public function actionBusiness($agent_sid)
    {
        yii::error([$_GET, $_POST, file_get_contents("php://input")]);               

        $agent = Agent::findOne(['sid' => $agent_sid]);       
        $suite = $agent->suite;

        $we = $suite->getQyWechat();
        if (isset($_GET['auth_code'])) {
            //$token = $we->setSuiteTicket($suite->suite_ticket)->getSuiteToken();
            //$permanentCode = $we->getPermanentCode($_GET['auth_code']);
            //$accessToken = $we->getAccessToken($auth_corpid, $permanentCode);
        }
        
        return 'success';        
    }    
}

