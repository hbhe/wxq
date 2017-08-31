<?php
use yii\helpers\Html;
use yii\grid\GridView;
use common\modules\attendance\models\department;
use common\modules\attendance\models\user;
/* @var $this yii\web\View */
/* @var $searchModel common\modules\attendance\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '员工列表';
$this->params['breadcrumbs'][] = $this->title;
//获取树形结构的部门列表
$department=department::getDepartment(true,'');
//获取职位列表
$leader=department::getLeader();
?>
<div class="user-index">
    <p>（1）在企业号的通讯录里，设置职务分别有：单位领导，部门领导，职工，退休四种状态，默认是职工。退休职工不会导入到后台。</p>
    <p>（2）如果部门也发送了变化，必须先同步部门，然后请点击“同步企业号员工列表”按钮，会自动同步企业通讯录。
       如果只是在通讯录中增加或改动了员工，只需要同步员工即可。
    </p>
    <p>（3）同步后，修改朱亚明局长的职务为局长，陈拥军局长为管理员。<?= Html::a('同步企业号员工列表', ['sync'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            //'userid',
            'name',
            [
                'attribute'=>'department',
                'value'=>function($model)use($department){
                    return user::idToName($model->department,$department);
                },
                'filter'=>$department,
            ],
            // 'position',
            // 'mobile',
            //'gender',
            // 'email:email',
            // 'weixinid',
            // 'avatar',
            [
                'attribute'=>'state',
                'value'=>function($model)use($is_work){
                    //休息时间，显示"休息时间"
                    if($is_work=='休息时间'){
                        return '休息时间';
                    }
                    //20分钟无信息，显示"异常情况"
                    if((time()-1200) > strtotime($model->update_at)){
                        return '异常情况';
                    }
                    $arr=[1=>'在单位',2=>'不在单位'];
                    return $arr[$model->state];
                },
                'filter'=>[1=>'在单位',2=>'不在单位'],
            ],
            [
                'attribute'=>'leader',
                'value'=>function($model)use($leader){
                    return $leader[$model->leader];
                },
                'filter'=>$leader,
            ],
            'update_at',
            'lng',
            'lat',
            ['class' => 'yii\grid\ActionColumn','template' => '{update} 　{delete}'],
        ],
    ]); ?>
</div>
