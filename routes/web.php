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


$router->group(['middleware' => 'public.api.auth'], function () use ($router) {
    $router->get('/', [function () {
        return 'Hello World!';
    }]);

    //** USERS **//
    $router->get('/users',['uses'=>'UserController@browse']);
    $router->get('/users/{user_id}',['uses'=>'UserController@read']);
    $router->put('/users/{user_id}',['uses'=>'UserController@edit']);
    $router->post('/users',['uses'=>'UserController@add']);
    $router->delete('/users/{user_id}',['uses'=>'UserController@delete']);

    //** TEAMS **//
    $router->get('/teams',['uses'=>'TeamController@browse']);
    $router->get('/teams/{team_id}',['uses'=>'TeamController@read']);
    $router->put('/teams/{team_id}',['uses'=>'TeamController@edit']);
    $router->post('/teams',['uses'=>'TeamController@add']);
    $router->delete('/teams/{team_id}',['uses'=>'TeamController@delete']);

    $router->get('/teams/{team_id}/members',['uses'=>'TeamController@list_members']);
    $router->post('/teams/{team_id}/members/{user_id}',['uses'=>'TeamController@add_member']);
    $router->delete('/teams/{team_id}/members/{user_id}',['uses'=>'TeamController@remove_member']);

    $router->get('/teams/{team_id}/messages',['uses'=>'TeamController@list_messages']);
    $router->post('/teams/{team_id}/messages/{user_id}',['uses'=>'TeamController@add_message']);

    $router->get('/teams/{team_id}/notes',['uses'=>'TeamController@list_notes']);
    $router->post('/teams/{team_id}/notes/{user_id}',['uses'=>'TeamController@add_note']);

    $router->get('/teams/{team_id}/scenario_logs',['uses'=>'TeamController@list_scenario_logs']);
    $router->post('/teams/{team_id}/scenario_logs/{user_id}',['uses'=>'TeamController@add_scenario_log']);


    //** ROLES **//
    $router->get('/roles',['uses'=>'RoleController@browse']);
    $router->get('/roles/{user_id}',['uses'=>'RoleController@read']);
    $router->put('/roles/{user_id}',['uses'=>'RoleController@edit']);
    $router->post('/roles',['uses'=>'RoleController@add']);
    $router->delete('/roles/{user_id}',['uses'=>'RoleController@delete']);
});
