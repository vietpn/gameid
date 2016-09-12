<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',

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
            'class' => 'api\modules\v1\Module'
        ],
        'v2' => [
            'basePath' => '@app/modules/v2',
            'class' => 'api\modules\v2\Module'
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'api\models\ApiUser',
            'enableAutoLogin' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning',],
                    'logFile' => '@runtime/logs/'.date('Y-m-d').'.error.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'trace'],
                    'exportInterval' => 1,
                    'logFile' => '@runtime/logs/'.date('Y-m-d').'.info.log'
                ]
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST /oauth2/<action:\w+>' => 'oauth2/default/<action>',
                'GET /active' => 'active/index',
                'GET /check-mo-smsplus' => 'check-mo-smsplus/index',
                'GET /smsplus1pay' => 'smsplus1pay/index',
                'GET /sms1pay' => 'sms1pay/index',

                // v1
                ['class' => 'yii\rest\UrlRule', 'controller' => ['v1/account'],],
                ['class' => 'yii\rest\UrlRule', 'controller' => ['v1/game-icash-history'],],

                'POST v1/accounts/login' => 'v1/account/login',
                'POST v1/accounts/social-login' => 'v1/account/social-login',
                // card charing with account id
                'POST v1/accounts/<id:\d+>/card-charging' => 'v1/account/card-charging',
                // card charging no account id
                'POST v1/accounts/card-charging-no-acc' => 'v1/account/card-charging-no-acc',
                // card buying with account id
                'POST v1/accounts/<id:\d+>/card-buying' => 'v1/account/card-buying',
                // card buying no account id
                'POST v1/accounts/card-buying-no-acc' => 'v1/account/card-buying-no-acc',
                'POST v1/accounts/<id:\d+>/topup-buying' => 'v1/account/topup-buying',
                'POST v1/accounts/<id:\d+>/coin' => 'v1/account/coin',
                'POST v1/accounts/<id:\d+>/upload-avatar' => 'v1/account/upload-avatar',
                'POST v1/accounts/<id:\d+>/email-verification' => 'v1/account/email-verification',
                'POST v1/accounts/<game_id:\d+>/reset-password' => 'v1/account/reset-password',
                // sms brand name
                'POST v1/smsbulk-brandname' => 'v1/account/smsbulk-brandname',
                // game-icash-history
                'POST v1/game-icash-histories/drawn-graph' => 'v1/game-icash-history/drawn-graph',
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
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211
                ]
            ],
            'useMemcached' => true,
            'serializer' => false,
        ],
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



