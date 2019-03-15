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

    $router->get('/services',['uses'=>'ServicesController@browse']);
    $router->get('/services/{service_id}',['uses'=>'ServicesController@read']);
    $router->get('/services/{service_id}/versions/latest',['uses'=>'ServicesController@latest_version']);
    $router->get('/services/{service_id}/versions',['uses'=>'ServicesController@versions']);
    $router->put('/services/{service_id}',['uses'=>'ServicesController@edit']);
    $router->put('/services/{service_id}/publish',['uses'=>'ServicesController@publish']);
    $router->put('/services/{service_id}/code',['uses'=>'ServicesController@code']);
    $router->post('/services',['uses'=>'ServicesController@add']);
    $router->delete('/services/{service_id}',['uses'=>'ServicesController@delete']);

    $router->get('/service_versions',['uses'=>'ServiceVersionsController@browse']);
    $router->get('/service_versions/{service_version_id}',['uses'=>'ServiceVersionsController@read']);
    $router->put('/service_versions/{service_version_id}',['uses'=>'ServiceVersionsController@edit']);
    $router->post('/service_versions',['uses'=>'ServiceVersionsController@add']);
    $router->delete('/service_versions/{service_version_id}',['uses'=>'ServiceVersionsController@delete']);
    
    $router->get('/service_instances',['uses'=>'ServiceInstancesController@browse']);
    $router->get('/service_instances/{service_instance_id}',['uses'=>'ServiceInstancesController@read']);
    $router->put('/service_instances/{service_instance_id}',['uses'=>'ServiceInstancesController@edit']);
    $router->post('/service_instances',['uses'=>'ServiceInstancesController@add']);
    $router->delete('/service_instances/{service_instance_id}',['uses'=>'ServiceInstancesController@delete']);
    
    $router->get('/resources',['uses'=>'ResourcesController@browse']);
    $router->get('/resources/{resource_id}',['uses'=>'ResourcesController@read']);
    $router->put('/resources/{resource_id}',['uses'=>'ResourcesController@edit']);
    $router->post('/resources',['uses'=>'ResourcesController@add']);
    $router->delete('/resources/{resource_id}',['uses'=>'ResourcesController@delete']);

    $router->get('/scheduler',['uses'=>'SchedulerController@browse']);
    $router->get('/scheduler/{scheduler_id}',['uses'=>'SchedulerController@read']);
    $router->get('/scheduler/{scheduler_id}/run',['uses'=>'SchedulerController@run']);
    $router->put('/scheduler/{scheduler_id}',['uses'=>'SchedulerController@edit']);
    $router->post('/scheduler',['uses'=>'SchedulerController@add']);
    $router->delete('/scheduler/{scheduler_id}',['uses'=>'SchedulerController@delete']);

    $router->get('/activity_log',['uses'=>'ActivityLogController@browse']);

    $router->get('/service_docs/{slug}',['uses'=>'DocumentationController@fetch']);
});

$router->get('/{slug}', ['uses'=>'DocumentationController@docs']);
$router->get('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->post('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->put('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->delete('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);

