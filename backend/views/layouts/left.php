<aside class="main-sidebar">

    <section class="sidebar">

<?php
/*
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
*/ ?>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    //['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    ['label' => '首页', 'icon' => 'dashboard', 'url' => ['/home']],
                    ['label' => '套件', 'icon' => 'file-code-o', 'url' => ['/suite']],
                    ['label' => '应用', 'icon' => 'users', 'url' => ['/agent']],
                    ['label' => '客户', 'icon' => 'users', 'url' => ['/corp']],
                    ['label' => '客户使用套件', 'icon' => 'users', 'url' => ['/corp-suite']],
                    ['label' => '客户使用应用', 'icon' => 'share', 'url' => ['/corp-agent']],
                    ['label' => '部门', 'icon' => 'home', 'url' => ['/department']],
                    ['label' => '员工', 'icon' => 'circle-o', 'url' => ['/employee']],

                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'visible' => false,
                        'label' => 'Same tools',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
