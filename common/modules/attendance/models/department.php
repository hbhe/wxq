<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_department".
 *
 * @property int $id
 * @property string $name 部门名称
 * @property string $parentid 父级id
 * @property int $order 排序
 */
class department extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_department';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['department_id','parentid', 'order'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['path'], 'string', 'max' => 100],
            [['corpid'], 'string', 'max' => 18],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_id'=>'部门id',
            'name' => '部门名称',
            'parentid' => '父级id',
            'order' => '排序',
            'path'=>'部门路径',
            'corpid'=>'企业id',
        ];
    }
    /**
     * @获取部门的树形结构列表或垂直列表
     * @param bool $tree true则是树形结构
     * @return [type] [部门数组]
     */
    public static function  getDepartment($tree=true,$corpid='')
    {
        if($corpid==''){$corpid=Yii::$app->session->get('corpid');}
        if($tree==false){
            //直接查询生成一维数组
            $department=(new \yii\db\Query())
                ->select(['name'])
                ->from('wxe_department')
                ->where(['corpid'=>$corpid])
                ->indexBy('department_id')
                ->column();
            return $department;
        }else{
            //查询结果为二维
            $department=(new \yii\db\Query())
                ->select(['department_id','name','path'])
                ->from('wxe_department')
                ->where(['corpid'=>$corpid])
                ->orderBy('path')
                ->all();
            $list=array();
            foreach ($department as  $v) {
                $n=substr_count($v['path'], ',');
                if($n>1){
                    $list[$v['department_id']]=str_repeat('　', $n).$v['name'];
                }else{
                    $list[$v['department_id']]=$v['name'];
                }
            }
            return $list;
        }
    }
    /**
     * @获取职位列表
     * @return [type] [职务数组]
     */
    public static function  getLeader()
    {
        $leader=array(0=>'单位领导',5=>'部门领导',10=>'普通员工');
        return $leader;
    }
    
}
