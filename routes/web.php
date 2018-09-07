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
    $router->get('/api_users/{apiuser_id}',['uses'=>'APIUsersController@read']);
    $router->put('/api_users/{apiuser_id}',['uses'=>'APIUsersController@edit']);
    $router->post('/api_users',['uses'=>'APIUsersController@add']);
    $router->delete('/api_users/{apiuser_id}',['uses'=>'APIUsersController@delete']);

    $router->get('/environments',['uses'=>'EnvironmentsController@browse']);
    $router->get('/environments/{environment_id}',['uses'=>'EnvironmentsController@read']);
    $router->put('/environments/{environment_id}',['uses'=>'EnvironmentsController@edit']);
    $router->post('/environments',['uses'=>'EnvironmentsController@add']);
    $router->delete('/environments/{environment_id}',['uses'=>'EnvironmentsController@delete']);

    $router->get('/modules',['uses'=>'ModulesController@browse']);
    $router->get('/modules/{module_id}',['uses'=>'ModulesController@read']);
    $router->get('/modules/{module_id}/versions',['uses'=>'ModulesController@versions']);
    $router->put('/modules/{module_id}',['uses'=>'ModulesController@edit']);
    $router->post('/modules',['uses'=>'ModulesController@add']);
    $router->delete('/modules/{module_id}',['uses'=>'ModulesController@delete']);

    $router->get('/module_versions',['uses'=>'ModuleVersionsController@browse']);
    $router->get('/module_versions/{module_version_id}',['uses'=>'ModuleVersionsController@read']);
    $router->put('/module_versions/{module_version_id}',['uses'=>'ModuleVersionsController@edit']);
    $router->post('/module_versions',['uses'=>'ModuleVersionsController@add']);
    $router->delete('/module_versions/{module_version_id}',['uses'=>'ModuleVersionsController@delete']);
    
    $router->get('/module_instances',['uses'=>'ModuleInstancesController@browse']);
    $router->get('/module_instances/{module_instance_id}',['uses'=>'ModuleInstancesController@read']);
    $router->put('/module_instances/{module_instance_id}',['uses'=>'ModuleInstancesController@edit']);
    $router->post('/module_instances',['uses'=>'ModuleInstancesController@add']);
    $router->delete('/module_instances/{module_instance_id}',['uses'=>'ModuleInstancesController@delete']);
    
    $router->get('/databases',['uses'=>'DatabasesController@browse']);
    $router->get('/databases/{database_id}',['uses'=>'DatabasesController@read']);
    $router->put('/databases/{database_id}',['uses'=>'DatabasesController@edit']);
    $router->post('/databases',['uses'=>'DatabasesController@add']);
    $router->delete('/databases/{database_id}',['uses'=>'DatabasesController@delete']);
    
    $router->get('/database_instances',['uses'=>'DatabaseInstancesController@browse']);
    $router->get('/database_instances/{database_instance_id}',['uses'=>'DatabaseInstancesController@read']);
    $router->put('/database_instances/{database_instance_id}',['uses'=>'DatabaseInstancesController@edit']);
    $router->post('/database_instances',['uses'=>'DatabaseInstancesController@add']);
    $router->delete('/database_instances/{database_instance_id}',['uses'=>'DatabaseInstancesController@delete']);

});

$router->get('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);


