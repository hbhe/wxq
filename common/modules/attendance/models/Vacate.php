<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_vacate".
 *
 * @property int $id
 * @property string $submitter 提交人userid
 * @property string $approver 审核人userid
 * @property int $approved 1=通过，0=未通过
 * @property string $reviewer 审批人userid
 * @property int $reviewed 1=通过，0=未通过
 * @property string $from_date 请假开始日期时间
 * @property string $to_date 请假结束日期时间
 * @property int $vacate_type 类型1=请假 2=休假
 * @property string $msg 请假理由
 * @property string $create_at 添加时间
 */
class Vacate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_vacate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['submitter'], 'required'],
            [['approved', 'reviewed', 'vacate_type','dayOrHalf'], 'integer'],
            [['from_date', 'to_date', 'create_at'], 'safe'],
            [['submitter', 'approver', 'reviewer'], 'string', 'max' => 64],
            [['msg'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'submitter' => '提交人userid',
            'approver' => '审核人userid',
            'approved' => '1=通过，0=未通过',
            'reviewer' => '审批人userid',
            'reviewed' => '1=通过，0=未通过',
            'dayOrHalf'=>'全天或半天',
            'from_date' => '请假开始日期时间',
            'to_date' => '请假结束日期时间',
            'vacate_type' => '类型3=请假 4=休假',
            'msg' => '请假理由',
            'create_at' => '添加时间',
        ];
    }
    public function type(){
        return array(3=>'请假',4=>'休假');
    }
    public function getType($type){
        $arr= $this->type();
        return $arr[$type];
    }
}
