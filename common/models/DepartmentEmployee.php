<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%department_employee}}".
 *
 * @property int $id
 * @property string $corp_id
 * @property string $department_id
 * @property int $employee_id
 * @property int $sort_order 排序
 * @property int $status
 */
class DepartmentEmployee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%department_employee}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_id'], 'required'],
            [['employee_id', 'sort_order', 'status'], 'integer'],
            [['corp_id', 'department_id'], 'string', 'max' => 128],
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
            'department_id' => 'Department ID',
            'employee_id' => 'Employee ID',
            'sort_order' => '排序',
            'status' => 'Status',
        ];
    }

    /**
     * @inheritdoc
     * @return DepartmentEmployeeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentEmployeeQuery(get_called_class());
    }
}
