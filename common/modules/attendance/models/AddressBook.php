<?php
namespace common\modules\attendance\models;
/**
* 
*/
class AddressBook 
{
	/**
     * 递归构造树形结构数组
     * @param  [type] $models [部门员工模型数组]
     * @param  [type] $pId    父类id
     * @return [type]   $trees  [树形结构]
     */
	public static function getTree($models, $pId)
	{
	    $trees =array();
	    foreach($models as $id => $model)
	    {
	       if( $pId ==$model['parentid'] ){
	        $model['parentid'] = AddressBook::getTree($models, $id);
	        $trees[] = $model;
	       }
	    }
	    return $trees;
	}
	/**
	 * 返回通讯录树形数组
	 * @param  [type]  $corpid        [企业号id]
	 * @param  string  $department_id [部门id]
	 * @param  integer $leader        [领导级别]
	 * @return [type]                 [树形数组]
	 */
	public static function book($corpid,$department_id='all',$leader=10,$mobile=''){        
		$departments=(new \yii\db\Query())
            ->select(['department_id','name','parentid',])
            ->from('wxe_department')
            ->all();
        // 构造部门模型数组
        $models=array();
        foreach ($departments as $k => $v) {
        	$models[$v['department_id']]['name']=$v['name'];
        	$models[$v['department_id']]['id']=$v['department_id'];
        	$models[$v['department_id']]['parentid']=$v['parentid'];
        }
        // 构造人员查询条件
        if($department_id=='all'){
        	$where=['corpid'=>$corpid];
        }elseif($leader==5 || $leader==0){// 如果是部门领导或者是单位领导,只出现单位领导的名单
    		$where[]='and';
    		$where[]=['corpid'=>$corpid];
			$where[]=['leader'=>0];
    		$where[]=['department'=>1];
    		if($mobile!=''){
                $where[]=['<>','mobile',$mobile];
            }
    	}elseif($leader==10){//如果是普通职工，只出现
    		$where=['and',['corpid'=>$corpid],['<','leader',$leader],['or',['department'=>$department_id],['like','department',$department_id]]];
    	}
    	// 按条件查询通讯录名单
        $users=(new \yii\db\Query())
            ->select(['userid','name','department'])
            ->from('wxe_department_user')
            ->orderBy('leader')
            ->where($where)
            ->all(); 
        //将人员填充到部门模型数组
        foreach ($users as  $user) {
        	if(strpos($user['department'], ';')===false){//属于单个部门
        		$models[$user['department']]['users'][]=['userid'=>$user['userid'],'name'=>$user['name']];
        	}else{//属于多个部门
        		// 遍历多个部门
        		$temp_arr=explode(';', rtrim($user['department'],';'));
        		foreach ($temp_arr as $v) {
        			$models[$v]['users'][]=['userid'=>$user['userid'],'name'=>$user['name']];
        		}
            }
        }
        // 树形结构数组
       return AddressBook::getTree($models,0);
	}
}