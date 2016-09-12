<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => 'CRM GDC-ID',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    // default language
    'language' => 'vi',
    'modules'=>[
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => '@app/views/layouts/main',
        ],

        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['admin'],
            'enableConfirmation' => false,
            'enableRegistration' => false,
            'enablePasswordRecovery' => false,
            'enableFlashMessages' => false,
            'modelMap' => [
                'LoginForm' => 'backend\models\LoginForm',
            ],
            'controllerMap' => [
                'security' => 'backend\controllers\user\SecurityController',
            ],
            //'layout' => '@app/views/layouts/login',
        ],

        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
    ],

    'aliases' => [
        '@mdm/admin' => '@app/extensions/mdm/yii2-admin',
    ],

    'components' => [
        'user' => [
            'identityClass' => 'dektrium\user\models\User',
        ],
        // override view Yii2-User login layout
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@app/views/user'
                ],
            ],
        ],

        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                    //'sourceLanguage' => 'vi',
                    //'fileMap' => [
                    //'app' => 'app.php',
                    //],
                ],
            ],
        ],

//        'user' => [
//            'identityClass' => 'common\models\User',
//            'enableAutoLogin' => true,
//        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/'.date('Y-m-d').'.log'
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        /*'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],*/
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => Yii::$app->params['google']['recaptcha']['siteKey'],
            'secret' => Yii::$app->params['google']['recaptcha']['secret'],
        ],
    ],

    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'user/security/login',
            'user/security/logout',
            'user/security/captcha',

//            'site/index',
//            'site/error',
//            'site/setting',
//
//            'admin/*',
//            'user/*',
//            'site/*',
//            'debug/*',
//            'gii/*',
//
//            'account/*',
//            'card-charging/*',
//            'card-buying/*',
//            'sms-charging/*',
//            'smsplus-charging/*',

            //'audit/*',
        ],
    ],

    'params' => $params,
];
