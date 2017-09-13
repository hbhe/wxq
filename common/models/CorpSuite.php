<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%corp_suite}}".
 *
 * @property int $id
 * @property string $corp_id
 * @property string $suite_id
 * @property string $permanent_code
 * @property string $accessToken
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class CorpSuite extends \common\wosotech\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%corp_suite}}';
    }

    /**
     * @inheritdoc
     * @return CorpSuiteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CorpSuiteQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['corp_id', 'suite_id'], 'string', 'max' => 128],
            [['permanent_code'], 'string', 'max' => 512],
            [['status'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'corp_id' => 'Corp ID',
            'suite_id' => 'Suite ID',
            'permanent_code' => 'Permanent Code',
            'accessToken' => 'Access Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getCorp()
    {
        return $this->hasOne(Corp::className(), ['corp_id' => 'corp_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    public function getSuite()
    {
        return $this->hasOne(Suite::className(), ['suite_id' => 'suite_id']);
    }

    public function getSuiteAccessToken()
    {
        $we = $this->suite->getQyWechat();
        $token = $we->getSuiteAccessToken($this->corp_id, $this->permanent_code);

        return $token;
    }

    public function afterDelete()
    {
        $agents = $this->suite->agents;
        foreach ($agents as $agent) {
            if (null !== ($model = CorpAgent::findOne(['corp_id' => $this->corp_id, 'agent_id' => $agent->id]))) {
                $model->delete();
            }
        }
        $this->trigger(self::EVENT_AFTER_DELETE);
    }

    /**
     * 从微信后台导入部门信息
     *
     */
    public function importDepartment()
    {
        $we = $this->getQyWechat();
        $rows = $we->getDepartment();
        Department::deleteAll(['corp_id' => $this->corp_id]);
        foreach ($rows['department'] as $row) {
            $model = new Department();
            $model->corp_id = $this->corp_id;
            $model->name = $row['name'];
            $model->id = implode('-', [$this->corp_id, $row['id']]);
            $model->sort_order = $row['order'];
            if ($row['parentid'] == 0) {
                if (!$model->makeRoot()->save()) {
                    Yii::error(['save db error', __METHOD__, __LINE__, $model->getErrors()]);
                    throw new Exception('save db error');
                }
            } else {
                $parent_id = implode('-', [$this->corp_id, $row['parentid']]);
                $parent = Department::findOne(['id' => $parent_id]);
                if (!$parent) {
                    Yii::error(['no parent', __METHOD__, __LINE__]);
                    throw new Exception('no parent');
                }
                if (!$model->appendTo($parent)->save()) {
                    Yii::error(['save db error', __METHOD__, __LINE__, $model->getErrors()]);
                    throw new Exception('save db error');
                }
            }
        }
    }

    public function getQyWechat()
    {
        $we = $this->suite->getQyWechat();
        $token = $we->getSuiteAccessToken($this->corp_id, $this->permanent_code);
        $we->checkAuth('', '', $token);

        return $we;
    }

    /**
     * 从微信后台导入员工信息
     *
     */
    public function importEmployee()
    {
        $we = $this->getQyWechat();
        $rows = $we->getUserListInfo(1, 1); // 获取department_id = 1（即所有）员工
        Yii::error($rows);
        return;

        /*
        *    "userlist": [
     *            {
            *                   "userid": "zhangsan",
     *                   "name": "李四",
     *                   "department": [1, 2],
     *                   "position": "后台工程师",
     *                   "mobile": "15913215421",
     *                   "gender": 1,     //性别。gender=0表示男，=1表示女
     *                   "tel": "62394",
     *                   "email": "zhangsan@gzdev.com",
     *                   "weixinid": "lisifordev",        //微信号
     *                   "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3W..../0",   //头像url。注：如果要获取小图将url最后的"/0"改成"/64"即可
     *                   "status": 1      //关注状态: 1=已关注，2=已冻结，4=未关注
            *                   "extattr": {"attrs":[{"name":"爱好","value":"旅游"},{"name":"卡号","value":"1234567234"}]}
     *            }
     *      ]
        */
        Employee::deleteAll(['corp_id' => $this->corp_id]);
        foreach ($rows['userlist'] as $row) {
            $model = new Employee();
            $model->setAttributes($row);
            $model->corp_id = $this->corp_id;
            //$model->name = $row['name'];
            //$model->id = implode('-', [$this->corp_id, $row['id']]);
            //$model->sort_order = $row['order'];
            if (!$model->save()) {
                Yii::error(['save db error', __METHOD__, __LINE__, $model->getErrors()]);
                throw new Exception('save db error');
            }

        }

    }
}
