<?php

return [

    'auth' => [
        'user' => env('AUTH_USER','graphene_proxyserver'),
        'password' => env('AUTH_PASSWORD','graphene_proxyserver'),
    ],

    'locale' => env('LOCALE','US/Eastern'),
    'key' => env('APP_KEY',''),
    'cipher' => 'AES-256-CBC',
];