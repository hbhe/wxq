<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_workday".
 *
 * @property int $id
 * @property string $date 日期
 * @property int $is_work_day 是否工作日
 */
class workday extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_workday';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'required'],
            [['date'], 'safe'],
            [['is_work_day'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => '日期',
            'is_work_day' => '是否工作日',
        ];
    }
    /**
     * 返回今年和明年的数组
     * @return array 返回数组
     */
    public function getYear(){
        $year=date('Y');//获取今天所属的年份
        $year=array($year=>'今年',($year+1)=>'明年');//形成数组
        return $year;
    }
    /**
     * 返回是否是工作日
     * @return array 返回数组
     */
    public function getDay(){
        $day=array(0=>'休息日',1=>'工作日');//形成数组
        return $day;
    }
    
}
