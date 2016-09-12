<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
//        'db' => [
//            'class' => 'yii\db\Connection',
//            'dsn' => 'mysql:host=localhost;dbname=ma_hr',
//            'username' => 'ma_hr',
//            'password' => 'PZGobPxGw2qfQ6eJ',
//            'charset' => 'utf8',
//            'tablePrefix' => 'cms_',
//        ],
        'cache' => [
            //'class' => 'yii\caching\FileCache',
            'class' => 'yii\caching\DummyCache',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
    ],
];
