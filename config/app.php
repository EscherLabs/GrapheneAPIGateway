<?php

return [

    'auth' => [
        'user' => env('AUTH_USER', isset($_SERVER['AUTH_USER'])?$_SERVER['AUTH_USER']:'graphene_proxyserver'),
        'password' => env('AUTH_PASSWORD', isset($_SERVER['AUTH_PASSWORD'])?$_SERVER['AUTH_PASSWORD']:'graphene_proxyserver'),
    ]

];