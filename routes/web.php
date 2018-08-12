<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => 'public.api.auth','prefix' => 'config_api'], function () use ($router) {

    //** USERS **//
    $router->get('/users',['uses'=>'UserController@browse']);
    $router->get('/users/{user_id}',['uses'=>'UserController@read']);
    $router->put('/users/{user_id}',['uses'=>'UserController@edit']);
    $router->post('/users',['uses'=>'UserController@add']);
    $router->delete('/users/{user_id}',['uses'=>'UserController@delete']);
});

$router->get('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);


