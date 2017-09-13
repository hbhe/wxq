<?php

namespace common\models;

use paulzi\adjacencyList\AdjacencyListBehavior;
use Yii;

/**
 * This is the model class for table "{{%department}}".
 *
 * @property string $id
 * @property string $corp_id
 * @property string $name 名称
 * @property string $parent_id 上级ID
 * @property int $sort_order 排序
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%department}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['sort_order'], 'integer'],
            [['id', 'corp_id', 'parent_id'], 'string', 'max' => 128],
            [['name'], 'string', 'max' => 64],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',  // 多个企业的部门都放在此表中，为避免数据重复, 表中的id 为corp_id + id(微信中的department_id)的形式,
            'corp_id' => 'Corp ID',
            'name' => '名称',
            'parent_id' => '上级ID',
            'sort_order' => '排序',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => AdjacencyListBehavior::className(),
                'parentAttribute' => 'parent_id',
                'sortable' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return DepartmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentQuery(get_called_class());
    }

}
