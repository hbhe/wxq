<?php
namespace frontend\controllers;

use common\models\Agent;
use common\models\Corp;
use common\models\CorpSuite;
use common\models\Employee;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Site controller
 */
class AgentEzoaController extends Controller
{
    //public $layout = 'main';
    public $layout = 'weui';
    /*
        public function behaviors()
        {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ];
        }
    */
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

    /*
     * http://127.0.0.1/wxq/frontend/web/index.php?r=agent-ezoa
     * http://127.0.0.1/wxq/common/wosotech/weui.js/dist/example/
     * http://127.0.0.1/wxq/common/wosotech/weui/dist/example/
     */
    public function actionIndex()
    {
        //return Yii::$app->user->identity->name . $this->route;
        return $this->render('grid');
    }

    public function actionSendMessage()
    {
        //return Yii::$app->user->identity->name . $this->route;
        return $this->render('send-message');
    }

}
