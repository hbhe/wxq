<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\ArrayHelper;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '今天是'.date('Y-m-d'),
        'brandUrl' => ['/attendance/admin/index'],
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    if(Yii::$app->session->get('corpid')===null){
        $url=\yii\helpers\Url::toRoute('/attendance/admin/login');
        header( "Location: $url" );
        exit(0);
    }else{
        if(Yii::$app->session->get('auth')=='admin'){
            $menuItems = [
                ['label' => '管理员登录', 'url' => ['/attendance/admin/index']],
                ['label' => '新闻公告管理', 'url' => ['/news/articals/index']],
                ['label' => '部门管理', 'url' => ['/attendance/department/index']],
                ['label' => '员工管理', 'url' => ['/attendance/user/index',]],
                ['label' => '工作日管理', 'url' => ['/attendance/workday/index']],
                // ['label' => '考勤查看', 'url' => ['/attendance/attendance/index']],
                ['label' => '考勤查看', 'url' => ['/attendance/new-attendance/index']],
                ['label' => '考勤统计', 'url' => ['/attendance/new-attendance/summary']],
                ['label' => '会议管理', 'url' => ['/attendance/meeting/index']],
                ['label' => '配置管理', 'url' => ['/attendance/configuration/index']],
                ['label' => '退出', 'url' => ['/attendance/admin/logout']]
            ];
        }elseif(Yii::$app->session->get('auth')=='xinwen'){
            $menuItems = [
                ['label' => '管理员登录', 'url' => ['/attendance/admin/index']],
                ['label' => '新闻管理', 'url' => ['/news/articals/index']],
                ['label' => '退出', 'url' => ['/attendance/admin/logout']]
            ];
        }elseif(Yii::$app->session->get('auth')=='gonggao'){
            $menuItems = [
                ['label' => '管理员登录', 'url' => ['/attendance/admin/index']],
                ['label' => '公告管理', 'url' => ['/news/articals/index']],
                ['label' => '退出', 'url' => ['/attendance/admin/logout']]
            ];
        }elseif(Yii::$app->session->get('auth')=='kaoqin'){
            $menuItems = [
                ['label' => '管理员登录', 'url' => ['/attendance/admin/index']],
                ['label' => '部门管理', 'url' => ['/attendance/department/index']],
                ['label' => '员工管理', 'url' => ['/attendance/user/index',]],
                ['label' => '工作日管理', 'url' => ['/attendance/workday/index']],
                ['label' => '考勤查看', 'url' => ['/attendance/new-attendance/index']],
                ['label' => '考勤统计', 'url' => ['/attendance/new-attendance/summary']],
                ['label' => '退出', 'url' => ['/attendance/admin/logout']]
            ];
        }elseif(Yii::$app->session->get('auth')=='huiyi'){
            $menuItems = [
                ['label' => '管理员登录', 'url' => ['/attendance/admin/index']],
                ['label' => '会议管理', 'url' => ['/attendance/meeting/index']],
                ['label' => '退出', 'url' => ['/attendance/admin/logout']]
            ];
        }
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink'=>[
                'label' => '首页',
                'url' => yii\helpers\Url::to(['/attendance/admin/index'])
            ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?php if(Yii::$app->session->hasFlash('alert')):?>
            <?php echo \yii\bootstrap\Alert::widget([
                'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
            ])?>
        <?php endif; ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= '技术支持：楚源盛互联网事业部' ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
