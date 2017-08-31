<?php

namespace common\modules\news\models;

use Yii;

/**
 * This is the model class for table "wxe_news_banners".
 *
 * @property int $id
 * @property string $name 栏目名称
 * @property string $create_at 添加时间
 * @property int $banner_type 栏目类型
 * @property int $order 排序
 * @property string $corpid 企业id
 */
class Banners extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_news_banners';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['create_at'], 'safe'],
            [['banner_type', 'order'], 'integer'],
            [['name'], 'string', 'max' => 50],
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
            'name' => '栏目名称',
            'create_at' => '添加时间',
            'banner_type' => '栏目类型',
            'order' => '显示排序',
            'corpid' => '企业id',
        ];
    }
    /**
     * 获取栏目类型数组
     * @return [array] [栏目类型]
     */
    public static function getType(){
        return [1=>'新闻类型',2=>'公告类型'];
    }
    /**
     * 获取栏目数组
     * @param  [string] $corpid [企业id]
     * @return [type]         [栏目数组]
     */
    public static function getBanners($corpid){
         return (new \yii\db\Query())
            ->select(['name'])
            ->from('wxe_news_banners')
            ->where(['corpid'=>$corpid])
            ->indexBy('id')
            ->column();
    }
}
