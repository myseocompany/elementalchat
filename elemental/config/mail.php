<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    |
    | Laravel supports both SMTP and PHP's "mail" function as drivers for the
    | sending of e-mail. You may specify which one you're using throughout
    | your application here. By default, Laravel is setup for SMTP mail.
    |
    | Supported: "smtp", "sendmail", "mailgun", "mandrill", "ses",
    |            "sparkpost", "log", "array"
    |
    */

    'driver' => env('MAIL_DRIVER', 'smtp'),

    'host' => env('MAIL_HOST',  'tiguan.websitewelcome.com'),
    'port' => env('MAIL_PORT',  465),

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'constructora@trujillogutierrez.com'),
        'name' => env('MAIL_FROM_NAME',  'Trujillo'),
    ],

    'encryption' => env('MAIL_ENCRYPTION',  'ssl'),
    'username' => env( 'clientes@pranhaurbano.com'),
    'password' => env( 'centrodenegocios2018'),
    'sendmail' => '/usr/sbin/sendmail -bs',

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
