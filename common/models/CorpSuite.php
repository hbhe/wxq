<?php

namespace common\models;

use Yii;
use yii\base\Exception;

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

    public function getQyWechat()
    {
        $we = $this->suite->getQyWechat();
        $token = $we->getSuiteAccessToken($this->corp_id, $this->permanent_code);
        $we->checkAuth('', '', $token);

        return $we;
    }


    /**
     * 导入通讯录
     *
     */
    public function importDepartmentEmployee()
    {
        $this->importDepartment();
        $this->importEmployee();
    }

    /**
     * 导入部门信息
     *
     */
    public function importDepartment()
    {
        if (0) {
            $rows = [
                'errcode' => 0,
                'errmsg' => 'ok',
                'department' => [
                    [
                        'id' => 1,
                        'name' => '武汉xxxx',
                        'parentid' => 0,
                        'order' => 2147483447,
                    ],
                    [
                        'id' => 2,
                        'name' => '市场部',
                        'parentid' => 1,
                        'order' => 100000000,
                    ],
                    [
                        'id' => 3,
                        'name' => '技术部',
                        'parentid' => 1,
                        'order' => 99999000,
                    ],
                ],
            ];
        } else {
            $we = $this->getQyWechat();
            $rows = $we->getDepartment();
        }
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

    /**
     * 导入员工信息
     *
     */
    public function importEmployee()
    {
        if (0) {
            $rows = [
                'errcode' => 0,
                'errmsg' => 'ok',
                'userlist' => [
                    [
                        'userid' => 'maxcvw',
                        'name' => 'caolei',
                        'department' => [
                            1, 2, 3
                        ],
                        'position' => '主管',
                        'gender' => '1',
                        'avatar' => 'http://p.qlogo.cn/bizmail/7KelGzSoy1RljgcMIiaomSVSKMzQlceq9gBicTVZ5yMvViblcGoOLdXIg/0',
                        'status' => 1,
                        'isleader' => 1,
                        'english_name' => 'jack',
                        'order' => [
                            0, 1
                        ],
                    ],
                    [
                        'userid' => 'fire-v',
                        'name' => 'xxx',
                        'department' => [
                            2,
                        ],
                        'position' => '后端开发',
                        'gender' => '1',
                        'avatar' => 'http://shp.qpic.cn/bizmp/YI2BzCzzDnbKoq9ryhWtxNM3JMrAMDCFM5DMtVDwQlaoH9NhCxibtvg/',
                        'status' => 1,
                        'isleader' => 0,
                        'english_name' => 'tom',
                        'order' => [
                            10,
                        ],
                    ]
                ],
            ];
        } else {
            $we = $this->getQyWechat();
            $rows = $we->getUserListInfo(1, 1); // 获取department_id = 1（即所有）员工
        }
        Yii::error($rows);

        Employee::deleteAll(['corp_id' => $this->corp_id]);
        DepartmentEmployee::deleteAll(['corp_id' => $this->corp_id]);
        foreach ($rows['userlist'] as $row) {
            Employee::importEmployeeOne($this->corp_id, $row);
            /*
            $model = new Employee();
            $model->setAttributes($row);
            $model->corp_id = $this->corp_id;
            if (!$model->save()) {
                Yii::error(['save db error', __METHOD__, __LINE__, $model->getErrors()]);
                throw new Exception('save db error');
            }
            $departments = $row['department'];
            $orders = $row['order'];
            foreach ($departments as $index => $id) {
                $department_id = implode('-', [$this->corp_id, $id]);
                $ar = DepartmentEmployee::findOne(['department_id' => $department_id, 'employee_id' => $model->id]);
                if ($ar === null) {
                    $ar = new DepartmentEmployee();
                    $ar->corp_id = $this->corp_id;
                    $ar->department_id = $department_id;
                    $ar->employee_id = $model->id;
                    $ar->sort_order = empty($orders[$index]) ? 0 : $orders[$index];
                    if (!$ar->save()) {
                        Yii::error(['save db error', __METHOD__, __LINE__, $ar->getErrors()]);
                        throw new Exception('save db error');
                    }
                }
            }
           */
        }

    }

}
