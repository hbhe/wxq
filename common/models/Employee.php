<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%employee}}".
 *
 * @property int $id
 * @property string $corp_id
 * @property string $userid
 * @property string $name 员工姓名
 * @property string $position 职位
 * @property string $mobile 手机
 * @property string $email 邮箱
 * @property string $avatar 头像
 * @property string $telephone 座机
 * @property string $english_name 英文名
 * @property string $extattr 扩展属性
 * @property int $gender 性别
 * @property int $isleader 是否为上级
 * @property int $status 状态
 * @property string $created_at
 * @property string $updated_at
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%employee}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid'], 'required'],
            [['gender', 'isleader', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['corp_id', 'userid', 'position'], 'string', 'max' => 128],
            [['name', 'mobile', 'email', 'telephone', 'english_name', 'extattr'], 'string', 'max' => 64],
            [['avatar'], 'string', 'max' => 256],
            [['mobile'], 'unique'],
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
            'userid' => 'Userid',
            'name' => '员工姓名',
            'position' => '职位',
            'mobile' => '手机',
            'email' => '邮箱',
            'avatar' => '头像',
            'telephone' => '座机',
            'english_name' => '英文名',
            'extattr' => '扩展属性',
            'gender' => '性别',
            'isleader' => '是否为上级',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return EmployeeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmployeeQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function getDepartments()
    {
        return $this->hasMany(Department::className(), ['id' => 'department_id'])->viaTable('{{%department_employee}}', ['employee_id' => 'id']);
    }

}