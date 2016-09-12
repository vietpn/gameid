<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-payment',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'payment\controllers',

    'modules' => [

        /*'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',

            'options' => [
                'token_param_name' => 'accessToken',
                'access_lifetime' => 3600,
            ],

            'storageMap' => [
                'user_credentials' => 'api\models\User',
            ],
            'grantTypes' => [
                'client_credentials' => [
                    'class' => '\OAuth2\GrantType\ClientCredentials',
                    'allow_public_clients' => false
                ],
                'user_credentials' => [
                    'class' => '\OAuth2\GrantType\UserCredentials',
                ],
                'refresh_token' => [
                    'class' => '\OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ],
            ]
        ],*/

        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'payment\modules\v1\Module'
        ],
        'v2' => [
            'basePath' => '@app/modules/v2',
            'class' => 'payment\modules\v2\Module'
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'payment\models\ApiUser',
            'enableAutoLogin' => false,
            'loginUrl' => null,
        ],
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                //'POST /oauth2/<action:\w+>' => 'oauth2/default/<action>',
                'GET /check-mo-smsplus1pay' => 'check-mo-smsplus1pay/index',
                'GET /smsplus1pay' => 'smsplus1pay/index',
                'GET /sms1pay' => 'sms1pay/index',

                // v1
                //['class' => 'yii\rest\UrlRule', 'controller' => ['v1/account'],],
            ],
        ],

        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
        ],

        /*'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,

            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],*/

//        'response' => [
//            'format' => yii\web\Response::FORMAT_JSON,
//            'charset' => 'UTF-8',
//        ],
//        'cache' => [
//            'class' => 'yii\caching\MemCache',
//            'servers' => [
//                [
//                    'host' => 'localhost',
//                    'port' => 11211
//                ]
//            ],
//        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
    ],
    'params' => $params,
];



