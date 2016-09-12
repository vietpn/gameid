<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace'],
                    'exportInterval' => 1,
                    'logFile' => '@runtime/logs/gdc-console-app.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'exportInterval' => 1,
                    'logFile' => '@runtime/logs/gdc-console-app.info.log'
                ]
            ],
        ],
    ],
    'params' => $params,
];
