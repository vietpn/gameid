<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    // card charging via pay365
    'pay365' => [
        'url' => 'http://charging.pay365.vn/CardCharging.asmx?WSDL',
        'agentcode' => 'igame',
        'agentkey' => 'cnc@igame#*2016',
        // list cate code
        'cate_code' => [
            'VINA' => 'Vinaphone',
            'MOBI' => 'MobiFone',
            'VTC' => 'VTC Vcoin',
            'GATE' => 'FPT Gate',
            'VT' => 'Viettel',
            'VNM' => 'Vietnam Mobile',
            'BIT' => 'Bit',
            'ZING' => 'Zing',
            'ONG' => 'On cash'
        ]
    ],

    // api tra thuong
    'softpin' => [
        'url' => 'http://testpay.pay365.vn/Softpin.asmx?WSDL',
        'agentcode' => 'softpin',
        'agentkey' => '123@#$cnc'
    ],

    // api form yotel
    'yotel' => [
        'sms_url' => 'http://api.tuquyk.com/api/payment/topup_sms',
        'sms_id_url' => 'http://api.tuquyk.com/api/payment/topup_sms_id ',
        'token' => '5A8386893A7C29D05422',
        'secret_key' => 'yotel@igame#*!2k',
        // ti le trong game
        'exchange_rate' => 0.4,
        // so tien cho phep sms
        'allow_amount' => [10000, 20000, 50000]
    ],

    // 1 pay
    '1pay' => [
        'secret_key' => 'rlt7d4sd4e7hki132cionj9wfmoda8ux',
        'smsplus_number' => 9029,
        'telco' => [
            1 => 'Viettel',
            2 => 'MobiFone',
            3 => 'Vinaphone',
            0 => 'Others'
        ],
    ],

    // rabbit queue
    'queue' => [
        'name' => 'yotel',
        'smsbulk_brandname' => 'smsbulk_brandname',
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'pass' => 'guest',
    ],


    // trang thai giao dich
    // 1: khoi tao, 2: dang xu ly, 3: ket thuc
    'tran_status_init' => 1,
    'tran_status_process' => 2,
    'tran_status_end' => 3,

    // status thanh cong, that bai
    'status_success' => 1,
    'status_fail' => 0,

    // time for memcached
    // 60 phut
    'time_cache' => 60 * 60,
    // 30 phut
    'time_cache_charging' => 30 * 60,

    // số lần nạp lỗi khóa thẻ
    'limit_time_charging' => 3,

    // key log application
    'key_log' => 'Game-ID',
    'key_log_card_charging' => 'CARD-CHARGING',
    'key_log_smsplus_charging' => 'SMSPLUS-CHARGING',
    'key_log_card_buying' => 'CARD-BUYING',
    'key_log_smsbulk_brandname' => 'SMSBULK-BRANDNAME',

    // sms brand name
    'sms_brandname' => [
        'url' => 'http://brandsms.vn:8018/VMGAPI.asmx?WSDL',
        'authenticateUser' => 'gdcvn',
        'authenticatePass' => 'vmg123456',
    ],
    //tổng doanh thu
    'gdc_total' => [
        'cnc' => 30,
        'one_pay' => 46,
        'yotel' => 20,
        'cps' => 37,
    ]
];
