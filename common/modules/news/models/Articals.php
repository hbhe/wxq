<?php

namespace common\modules\news\models;
use yii\helpers\HtmlPurifier;
use Yii;

/**
 * This is the model class for table "wxe_news_articals".
 *
 * @property int $id
 * @property string $banner_id 栏目id
 * @property string $author 作者
 * @property string $title 文章题目
 * @property string $img 图文中的图片
 * @property string $artical 文章内容
 * @property string $create_at 添加时间
 * @property int $issue 是否发布
 * @property int $order 排序
 * @property string $corpid 企业id
 */
class Articals extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_news_articals';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['banner_id', 'author', 'title'], 'required'],
            [['banner_id', 'img', 'issue', 'order','click'], 'integer'],
            [['artical'], 'string'],
            [['create_at'], 'safe'],
            [['author', 'title'], 'string', 'max' => 30],
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
            'banner_id' => '栏目',
            'author' => '作者',
            'title' => '文章题目',
            'img' => '图文中的图片',
            'artical' => '文章内容',
            'create_at' => '添加时间',
            'issue' => '是否发布',
            'order' => '排序（数字越大，排列越靠前，如果不设置按日期时间排列）',
            'corpid' => '企业id',
            'click'=>'点击量',
        ];
    }
    /**
     * 过滤文章内容中的代码
     * @param  [type] $insert [是否是插入]
     * @return [type]         [是否成功]
     */
    public function beforeSave($insert){
        if(parent::beforeSave($insert)){
            // var_dump($this->img);die;
            if($this->img=='')$this->img=0;
            $this->title=HtmlPurifier::process($this->title);
            $this->artical= HtmlPurifier::process($this->artical);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 是否审核、发布的数组的键值对
     * @return boolean [description]
     */
    public function isIssue(){
        return [0=>'未审核',1=>'发布'];
    }
}
