<?php

return [

    'auth' => [
        'user' => env('AUTH_USER', isset($_SERVER['AUTH_USER'])?$_SERVER['AUTH_USER']:'graphene_proxyserver'),
        'password' => env('AUTH_PASSWORD', isset($_SERVER['AUTH_PASSWORD'])?$_SERVER['AUTH_PASSWORD']:'graphene_proxyserver'),
    ],

    'locale' => env('LOCALE', isset($_SERVER['LOCALE'])?$_SERVER['LOCALE']:'US/Eastern'),
    'key' => env('APP_KEY',''),
    'cipher' => 'AES-256-CBC',
];