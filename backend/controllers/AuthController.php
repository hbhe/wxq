<?php
namespace backend\controllers;

use common\models\Corp;
use common\models\CorpAgent;
use common\models\CorpSuite;
use common\models\Suite;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class AuthController extends Controller
{
    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * 系统事件接收URL, 套件在授权发生变化时，会推送一些事件过来，此url就是用来处理这个事
     *
     * http://wxq-admin.buy027.com/index.php?r=auth&suite_sid=ezoa
     *
     * @param $suite_sid
     * @return string
     */
    public function actionIndex($suite_sid)
    {
        /*
        [
            'r' => 'auth',
            'msg_signature' => 'df39c7b1f7faa629b95bc0a6ca346002e3ce8a65',
            'timestamp' => '1492585060',
            'nonce' => '938321678',
            'echostr' => 'pxf6EB5S6EUI4AGyJ+u4WvvQQwDV4yx7KSTmpiwYU3okE5Rm4p/h9PLtSyOZWwOVTWGr7oUJvZFpupeIws5rJg==',
        ],
        or
        [
            'r' => 'auth',
            'suite_sid' => 'ezoa',
            'msg_signature' => '9c6a3c2e8ddffedba7ad6e96af053d727bcb4cfc',
            'timestamp' => '1504669113',
            'nonce' => '1874753845',
        ],
        <xml><ToUserName><![CDATA[tj4a1744a4a878638f]]></ToUserName>
        <Encrypt><![CDATA[lY7+o/dRNf+ZxSq5S27Vujur9E9pf8C0leeNJ6A2VdZSuUXeRzFeaKw55Nj37K7W1SBHzAHpCFGUfeENmMqb06SBjL8RyBIVLbjFU/+YzHjEgQSCj5Hl2coIQwIo7Y3dAyuzHhxvqLcmqfhxqrWNRfvtjOALxjUMigrJ55hfCHUvkgksh6O229OdiApLks/fX0WBB1nlIxtUyvuq9Bw0XlO51gO/pY7lw58CQXXGkFl6yYfzkauAGuhsgQDyjwN/yTvIbPBBA68aWeRg4DFY5667ZVlrCP1DOr0JDy0gX0rGh4kA/RNrUBx0NnlocKIL9z5JLcnF6KgtyDnnnF17TrNvroY/MiEnMapGEjsqv17hW7G7fgbDHq4BmgL1hQJV]]></Encrypt>
        <AgentID><![CDATA[]]></AgentID>
        </xml>
        */
        Yii::error([$_GET, $_POST, file_get_contents("php://input")]);

        $suite = Suite::findOne(['sid' => $suite_sid]);
        if (null === $suite) {
            Yii::error([$_GET, $_POST, file_get_contents("php://input")]);
            return 'err';
        }
        $we = $suite->getQyWechat();
        if (!$we->valid()) {
            Yii::error(['decrypt failed.', $_GET, $_POST, file_get_contents("php://input"), $options]);
            Yii::$app->end();
        }

        $we->getRev();
        $data = $we->getRevData();
        Yii::error(['decrypt data', $data]);

        switch ($data['InfoType']) {
            case 'suite_ticket':
                // 心跳时刷新suite_ticket
                /*
                [
                    'SuiteId' => 'tj8c2445c93840db09',
                    'SuiteTicket' => 'J6zl8J5U_tF6Tq6BHI72hdn8Poo7-LIli9p7peAPtr-pVIfsEoAdQRIz3rg-WR_W',
                    'InfoType' => 'suite_ticket',
                    'TimeStamp' => '1492741808',
                    'AgentID' => '',
                ],
                */
                $suite->suite_ticket = $data['SuiteTicket'];
                if (!$suite->save()) {
                    Yii::error([__METHOD__, __LINE__, $suite->getErrors()]);
                }
                break;

            case 'create_auth':
                // 客户安装套件时
                /*
                [
                    'SuiteId' => 'tj8c2445c93840db09',
                    'AuthCode' => 'd8QsSuc2dIjEVZLfONXdGGLeN2ihnQ0RsnENtAgRNrA38IKmjfKv709cWTdQr5_5',
                    'InfoType' => 'create_auth',
                    'TimeStamp' => '1493711908',
                    'AgentID' => '',
                ]
                */
                $arr = $we->getPermanentCode($data['AuthCode']);
                if (false === $arr) {
                    Yii::error(['getPermanentCode error', __METHOD__, __LINE__]);
                    Yii::$app->end();
                }

                $auth_corp_info = $arr['auth_corp_info'];
                $corp_id = $auth_corp_info['corpid'];
                $auth_info = $arr['auth_info'];
                $agents = $auth_info['agent'];

                // 保存客户信息
                $model = Corp::findOne(['corp_id' => $corp_id]);
                if (null === $model) {
                    $model = new Corp();
                    $model->corp_id = $corp_id;
                }
                $model->setAttributes($auth_corp_info);
                if (!$model->save(false)) {
                    Yii::error(['save Corp err', __METHOD__, __LINE__, $model->toArray(), $model->getErrors()]);
                    Yii::$app->end();
                }

                // 保存客户订购的suite
                $model = CorpSuite::findOne(['corp_id' => $corp_id, 'suite_id' => $suite->suite_id]);
                if (null === $model) {
                    $model = new CorpSuite();
                }
                $model->corp_id = $corp_id;
                $model->suite_id = $suite->suite_id;
                $model->permanent_code = $arr['permanent_code'];

                $model->setAttributes($auth_corp_info);
                if (!$model->save(false)) {
                    Yii::error(['save CorpSuite err', __METHOD__, __LINE__, $model->toArray(), $model->getErrors()]);
                    Yii::$app->end();
                }

                // 先保存agentid, 此时还没有agent_sid, 在agent/callback时才将2者关联起来!
                foreach ($agents as $agent) {
                    $model = CorpAgent::findOne(['corp_id' => $corp_id, 'agentid' => $agent['agentid']]);
                    if (null === $model) {
                        $model = new CorpAgent();
                    }
                    $model->corp_id = $corp_id;
                    $model->agentid = $agent['agentid'];

                    $model->setAttributes($agent);
                    if (!$model->save(false)) {
                        Yii::error(['save CorpAgent err', __METHOD__, __LINE__, $model->toArray(), $model->getErrors()]);
                        Yii::$app->end();
                    }
                }
                break;

            case 'change_auth':
                /*
                [
                    'SuiteId' => 'tj6fa3713d6ad487a1',
                    'InfoType' => 'cancel_auth',
                    'TimeStamp' => '1506672838',
                    'AuthCorpId' => 'wxe675e8d30802ff44',
                    'AgentID' => '',
                ],
                */
                $authCorpId = $data['AuthCorpId'];
                break;

            case 'cancel_auth':
                // 用户删除订购的套件
                /*
                [
                    'SuiteId' => 'tj8c2445c93840db09',
                    'AuthCorpId' => 'wxe675e8d30802ff44',
                    'InfoType' => 'cancel_auth',
                    'TimeStamp' => '1493363816',
                    'AgentID' => '',
                ],
                */
                $authCorpId = $data['AuthCorpId'];
                $SuiteId = $data['SuiteId'];
                $model = CorpSuite::findOne(['corp_id' => $authCorpId, 'suite_id' => $SuiteId]);
                if (null !== $model) {
                    $model->delete();
                }
                break;

            case 'contact_sync':
                /*
                [
                    'SuiteId' => 'tj8c2445c93840db09',
                    'Seq' => '2',
                    'InfoType' => 'contact_sync',
                    'TimeStamp' => '1493365335',
                    'AuthCorpId' => 'wxe675e8d30802ff44',
                    'AgentID' => '',
                ],
                */
                $authCorpId = $data['AuthCorpId'];
                $SuiteId = $data['SuiteId'];
                $model = CorpSuite::findOne(['corp_id' => $corp_id, 'suite_id' => $SuiteId]);
                //$accessToken = $model->getAccessToken();
                break;

            default:
                break;
        }

        return 'success';
    }


}

