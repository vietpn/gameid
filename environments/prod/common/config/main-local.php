<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=gdc_game_id',
            'username' => 'gdc_game_id',
            'password' => '7vqwrSbFHDmtGG0b',
            'charset' => 'utf8',
            'tablePrefix' => 'cms_',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,//set this property to false to send mails to real email addresses
            //comment the following array to send mail using php's mail function
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'info.gdcvn@gmail.com',
                'password' => '17814365195715930de5130',
                'port' => '587', // Port 25 is a very common port too
                'encryption' => 'tls', // It is often used, check your provider or mail server
            ],
        ],
        // yii2-admin
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['default'],
            // 'class' => 'yii\rbac\PhpManager',
        ],
    ],
];
