<?php

return [
    'app' => [
        'url' => 'http://hebecollins.com',
        'hash' => [
            'algo'=> PASSWORD_BCRYPT,
            'cost'=> 10
        ]
    ],

    'db' => [
        'driver'=> 'mysql',
        'host'=>'127.0.0.1',
        'name'=>'hebecollins',
        'username'=>'root',
        'password'=>'',
        'charset'=>'utf8',
        'collation'=>'utf8_unicode_ci',
        'prefix'=>''
    ],

    'auth' => [
        'session'=>'user_id',
        'remember'=>'user_r'
    ],

    'mail'=>[
        'smtp_auth'=>true,
        'smtp_secure'=>'tls',
        'host'=>'smtp.gmail.com',
        'password'=>'nokialumia630',
        'port'=>587,
        'html'=>true
    ],
    'twig'=>[
        'debug'=>true
    ],
    'crsf'=>[
        'session'=>'crsf_token'
    ]
];
