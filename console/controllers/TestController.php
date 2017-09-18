<?php

namespace console\controllers;

use common\models\Department;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\models\WxGh;
use common\models\WxUser;
use common\wosotech\Util;
use common\models\Suite;
use common\models\CorpSuite;

use wechat\models\QyWechat;

class TestController extends \yii\console\Controller
{

    public function init()
    {
        Yii::$app->getUrlManager()->setBaseUrl('/wx/web/index.php');
        Yii::$app->getUrlManager()->setHostInfo('http://xxx.com');
        Yii::$app->getUrlManager()->setScriptUrl('/wx/web/index.php');
    }

    // php yii test/sync wx0b4f26d460868a25 7
    public function actionSync($appid, $agentid)
    {
        $gh = WxGh::findOne(['appid' => 'wx0b4f26d460868a25', 'gh_id' => 7]);
        $we = $gh->getQyWechat();
        $rows = $we->getUserListInfo(1, 1);
        var_dump($rows);
    }

    // php yii test/get-suite-token
    public function actionGetSuiteToken()
    {
        $model = Suite::findOne(['suite_id' => 'tj8c2445c93840db09']);
        $we = $model->getQyWechat();
        $token = $we->getSuiteToken();
        var_dump($token);
    }

    // php yii test/get-agent-list
    public function actionGetAgentList()
    {
        set_time_limit(60); // in seconds       
        if (!\Yii::$app->mutex->acquire(__METHOD__, 0)) {
            exit;
        }

        // $model = CorpSuite::findOne(['corp_id' => 'wx0b4f26d460868a25', 'suite_id' => 'tj8c2445c93840db09']);
        $model = CorpSuite::findOne(['corp_id' => 'wxe675e8d30802ff44', 'suite_id' => 'tj8c2445c93840db09']);
        $we = $model->getQyWechat();

        $rows = $we->getDepartment();
        yii::error($rows);

        $rows = $we->getAgentList();
        yii::error($rows);

        $agentid = $rows['agentlist'][0]['agentid'];
        $rows = $we->getAgent($agentid);
        yii::error($rows);

        $rows = $we->getMenu($agentid);
        yii::error($rows);

        $menu = [
            'button' => [
                [
                    'type' => 'view',
                    'name' => 'AAA',
                    'url' => 'http://baidu.com',
                ],
                [
                    'type' => 'click',
                    'name' => 'BBB',
                    'key' => 'BBB',
                ],

                [
                    'name' => 'DropDown',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => 'DropDown-1',
                            'url' => 'http://sina.com',
                        ],
                        [
                            'type' => 'click',
                            'name' => 'DropDown-2',
                            'key' => 'DropDown-2',
                        ],

                    ],
                ],
            ],
        ];

        $we->createMenu($menu, $agentid);

        \Yii::$app->mutex->release(__METHOD__);
    }

    // php yii test/get-department
    public function actionGetDepartment()
    {
        $model = CorpSuite::findOne(['corp_id' => 'wxe675e8d30802ff44', 'suite_id' => 'tj6fa3713d6ad487a1']);
        $model->importDepartment();
        $model->importEmployee();
    }
}
