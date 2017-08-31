<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\ArrayHelper;
AppAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\modules\service\models\BookServiceLogin */
/* @var $form yii\bootstrap\ActiveForm */
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
    $week=array('天','一','二','三','四','五','六');
    NavBar::begin([
        'brandLabel' => '今天是'.date('Y-m-d').'　　星期'.$week[date('w',time())],
        'brandUrl' => ['default/index'],
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    NavBar::end();
    ?>

    <div class="container">
    	<?= Alert::widget() ?>
        <?php if(Yii::$app->session->hasFlash('alert')):?>
            <?php echo \yii\bootstrap\Alert::widget([
                'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
            ])?>
        <?php endif; ?>
    </div>
	    <div class="admin-login-form">

	    <?php $form = ActiveForm::begin(); ?>

	    <?php echo $form->errorSummary($model); ?>

	    <?php echo $form->field($model, 'admin')->textInput(['maxlength' => true]) ?>

	    <?php echo $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

	    <div class="form-group">
	        <?php echo Html::submitButton('登录', ['class' =>'btn btn-primary']) ?>
	    </div>

	    <?php ActiveForm::end(); ?>

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