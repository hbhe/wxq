<?php

namespace common\modules\news\controllers;
use yii;
use yii\web\Controller;

/**
 * Default controller for the `news` module
 */
class DefaultController extends Controller
{
	public $enableCsrfValidation = false;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    	//获取参数
    	$corpid=Yii::$app->request->get('corpid','ww1de94f786c71be9f');
    	$banner_type=Yii::$app->request->get('banner_type',1);
    	//根据参数获取baner_id
    	$banner=(new \yii\db\Query())
    		->select('id,name')
    		->from('wxe_news_banners')
    		->where(['corpid'=>$corpid,'banner_type'=>$banner_type])
    		->one();
    	$banner_id=$banner['id'];
    	$banner_name=$banner['name'];
    	//初始化where条件
    	$where=['corpid'=>$corpid,'banner_id'=>$banner_id,'issue'=>1];
    	//如果有搜索表单提交则更改where条件
    	if(Yii::$app->request->isPost){
    		$search=Yii::$app->request->post('search');
    		if(!empty($search)){
    			$where=['and',['banner_id'=>$banner_id],['issue'=>1],['like','title',$search]];
    		}
    	}
    	//根据条件，查询结果
    	$query=(new \yii\db\Query())
    		->select('id,title,img,create_at,click')
    		->from('wxe_news_articals')
    		->where($where)
    		->orderBy('id desc');
    	$pages=new \yii\data\Pagination(['totalCount'=>$query->count()]);
    	$pages->defaultPageSize = 8;
    	$models=$query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->renderPartial('index',['models'=>$models,'pages'=>$pages,'banner_name'=>$banner_name,'banner_type'=>$banner_type,'corpid'=>$corpid]);
    }
    /**
     * 查看详细页
     * @param  [int] $id artical_id
     * @return [type]  mixed [description]
     */
    public function actionView($id){
    	//根据id查找文章内容
    	$artical=(new \yii\db\Query())
    		->from('wxe_news_articals')
    		->where(['id'=>$id])
    		->one();
        if($artical==null){
            header("Content-type: text/html; charset=utf-8");
            echo '数据已经被删除！';
            exit(0);
        }
    	//点击次数加1
    	Yii::$app->db->createCommand()
    		->update('wxe_news_articals',['click'=>$artical['click']+1 ],'id='.$artical['id'])
    		->execute();
    	return $this->renderPartial('view',['artical'=>$artical]);
    }
}
