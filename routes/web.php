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

$router->get('/', [function () {
    return 'Welcome!';
}]);

$router->group(['middleware' => 'public.api.auth','prefix' => 'api'], function () use ($router) {
    $router->get('/api_users',['uses'=>'APIUsersController@browse']);
    $router->get('/environments',['uses'=>'EnvironmentsController@browse']);
    $router->get('/modules',['uses'=>'ModulesController@browse']);
    $router->get('/module_versions',['uses'=>'ModuleVersionsController@browse']);
    $router->get('/module_instances',['uses'=>'ModuleInstancesController@browse']);
    $router->get('/module_instances/{module_instance_id}',['uses'=>'ModuleInstancesController@read']);
    $router->get('/databases',['uses'=>'DatabasesController@browse']);
    $router->get('/database_instances',['uses'=>'DatabaseInstancesController@browse']);
    $router->get('/database_instances/{database_instance_id}',['uses'=>'DatabaseInstancesController@read']);
});

$router->get('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);


