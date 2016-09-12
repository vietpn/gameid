<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
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
        'brandLabel' => 'GDC - ID',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('menu', 'Login'), 'url' => ['/user/security/login']];
    } else {
        $menuItems = [
            ['label' => Yii::t('menu', 'Account'), 'url' => ['#'],
                'items' => [
                    ['label' => Yii::t('menu', 'GDC Account'), 'url' => ['/account/index'],],
                    ['label' => Yii::t('menu', 'Social Account'), 'url' => ['/social-account/index'],],
                ]
            ],
            ['label' => Yii::t('menu', 'Trả thưởng'), 'url' => ['#'],
                'items' => [
                    ['label' => Yii::t('menu', 'Trả thưởng thẻ ĐT'), 'url' => ['/card-buying/index'],],
                    ['label' => Yii::t('menu', 'Trả thưởng TKĐT'), 'url' => ['/topup-buying/index'],],
                ]
            ],
            ['label' => Yii::t('menu', 'Nạp thẻ'), 'url' => ['/card-charging/index'],],
            ['label' => Yii::t('menu', 'SMS'), 'url' => ['#'],
                'items' => [
                    ['label' => Yii::t('menu', 'SMS Kích hoạt'), 'url' => ['/sms-charging/index'],],
                    ['label' => Yii::t('menu', 'SMS Plus Charging'), 'url' => ['/smsplus-charging/index'],],
                    ['label' => Yii::t('menu', 'SMS Bulk Brand Name'), 'url' => ['/smsbulk-brandname/index'],],
                ]

            ],
            ['label' => Yii::t('menu', 'Cấu hình'), 'url' => ['/config/index'],],
            [
                'label' => '<i class="glyphicon glyphicon-cog"></i> ' . Yii::t('menu', 'System'), 'url' => ['#'],
                'items' => [
                    ['label' => Yii::t('menu', 'Manage users'), 'url' => ['/user/admin/index'],],
                    ['label' => Yii::t('menu', 'Manage assignment'), 'url' => ['/admin/assignment'],],
                    ['label' => Yii::t('menu', 'Quản lý vai trò'), 'url' => ['/admin/role'],],
                    ['label' => Yii::t('menu', 'Quản lý quyền'), 'url' => ['/admin/permission'],],
                    ['label' => Yii::t('menu', 'Quản lý Route'), 'url' => ['/admin/route'],],
                    ['label' => Yii::t('menu', 'Quản lý Rule'), 'url' => ['/admin/rule'],],
                ],
            ],
        ];

        /*$menuItems[] = [

            'label' => '<i class="glyphicon glyphicon-cog"></i> ' . Yii::t('profile', 'Language'), 'url' => ['#'],
            'items' => [
                ['label' => Yii::t('profile', 'Lang Vi'), 'url' => ['/site/setting', 'lang' => 'vi' ],],
                ['label' => Yii::t('profile', 'Lang Cn'), 'url' => ['/site/setting', 'lang' => 'zh-CN' ],],
            ],
        ];*/

        $menuItems[] = [
            'label' => '<i class="glyphicon glyphicon-user"></i> ' . Yii::$app->user->identity->username,
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-info-sign"></span>' . Yii::t('menu', 'Profile'),
                    'url' => ['/user/settings/profile'],
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-log-out"></span> ' . Yii::t('menu', 'Logout'),
                    'url' => ['/user/security/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ]
            ]
        ];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; GDC-ID <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
