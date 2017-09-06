<?php

namespace backend\controllers;

use common\models\Corp;
use common\models\CorpAgent;
use common\models\CorpSuite;
use Yii;
use common\models\Agent;
use common\models\AgentSearch;
use common\wosotech\base\Controller;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AgentController implements the CRUD actions for Agent model.
 */
class AgentController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Agent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Agent model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Agent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Agent();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
	        return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Agent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
	        return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Agent model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Agent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Agent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 当微信用户发消息时, 每个应用(Agent)都要设置一个处理url来处理消息, 支持$CORPID$模板变量, 不过url参数中这个corpid是服务商的corpid,没什么用? ToUserName才是使用者企业corpid
     * http://wxq-admin.buy027.com/index.php?r=agent/callback&agent_sid=ezoa-agent&corpid=$CORPID$
     *
     * @return string
     */
    public function actionCallback()
    {
        /*
        [
            'r' => 'agent/callback',
            'agent_sid' => 'ezoa-agent',
            'corpid' => 'wx0b4f26d460868a25',
            'msg_signature' => '1bca8057e4f3f9ec1c39741e936f3f69a5726d85',
            'timestamp' => '1504680998',
            'nonce' => '15365629',
            'echostr' => 'PnCGxCL4fQbBaWznBVh9+OMa5HwEmaSPKTrBewU4O0XcfVaUyfuX7anTil9PP6c/LtQN2zVwobQSEl8gcAv6gQ==',
        ],
        [
            'r' => 'auth/callback'
            'agent_sid' => 'ezoa-agent'
            'corpid' => 'wxe675e8d30802ff44'
            'msg_signature' => 'f0bd5d86632292174fcae6fbcacca97b313e19e0'
            'timestamp' => '1504665259'
            'nonce' => '904589940'
        ]
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
        Yii::error([__METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);

        if (empty($agent_sid = Yii::$app->request->get('agent_sid'))) {
            Yii::error(['no agent_id parameter', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no agent_id parameter');
        }
/*
        if (empty($corpid = Yii::$app->request->get('corpid'))) {
            Yii::error(['no corpid parameter', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            //throw new Exception('no corpid parameter');
            Yii::$app->end();
        }

        $corp = Corp::findOne(['corp_id' => $corpid]);
        if ($corp === null) {
            Yii::error(['no corp', __METHOD__, __LINE__, $_GET, $_POST, file_get_contents("php://input")]);
            throw new Exception('no corp');
        }
*/
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

        $we = $suite->getQyWechat();
        if (!$we->valid()) {
            Yii::error(['decrypt failed.', $_GET, $_POST, file_get_contents("php://input"), $options]);
            Yii::$app->end();
        }

        $we->getRev();
        $data = $we->getRevData();
        Yii::error(['data', $data]);
        if (isset($data['Event']) && 'subscribe' == $data['Event']) {
            $model = CorpAgent::findOne(['corp_id' => $data['ToUserName'], 'agent_id' => $agent->id]);
            if (null === $model) {
                $model = new CorpAgent();
                $model->corp_id = $data['ToUserName'];
                $model->agent_id = $agent->id;
                if (!$model->save()) {
                    Yii::error(['save CorpAgent err', __METHOD__, __LINE__, $model->getErrors()]);
                } else Yii::error('save CorpAgent ok' . $agent->id);
            }

            $model = CorpSuite::findOne(['corp_id' => $data['ToUserName'], 'suite_id' => $suite->suite_id]);
            if (null === $model) {
                $model = new CorpSuite();
                $model->corp_id = $data['ToUserName'];
                $model->suite_id = $suite->suite_id;
                if (!$model->save()) {
                    Yii::error(['save CorpSuite err', __METHOD__, __LINE__, $model->getErrors()]);
                }
            }

        }

        return 'success';
    }

    /**
     * Agent业务设置(Agent管理后台)URL, 支持$CORPID$变量
     * http://wxq-admin.buy027.com/index.php?r=agent/backend&agent_sid=ezoa-agent&corpid=$CORPID$
     *
     * 例如：http://wxq-admin.buy027.com/index.php?r=agent/backend&agent_sid=ezoa-agent&corpid=wxe675e8d30802ff44&auth_code=t3ArVy4uetdevIg8PBDl9ilL640sQ-Q6mfbQ6o4a8MCCGH7P5VAugz3SCUCiallsq6C1fvmbZPL3GJAtveWIOQ
     * @param $agent_sid
     * @return string
     */
    public function actionBackend()
    {
        /*
        [
            'r' => 'agent/backend',
            'agent_sid' => 'ezoa-agent',
            'corpid' => 'wxe675e8d30802ff44',
            'auth_code' => 't3ArVy4uetdevIg8PBDl9ilL640sQ-Q6mfbQ6o4a8MCCGH7P5VAugz3SCUCiallsq6C1fvmbZPL3GJAtveWIOQ',
        ]
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

        $we = $suite->getQyWechat();
        if (isset($_GET['auth_code'])) {
            //$token = $we->setSuiteTicket($suite->suite_ticket)->getSuiteToken();
            //$permanentCode = $we->getPermanentCode($_GET['auth_code']);
            //$accessToken = $we->getAccessToken($auth_corpid, $permanentCode);
        }

        return 'success';
    }

}
