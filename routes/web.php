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

    $router->get('/apis',['uses'=>'APIsController@browse']);
    $router->get('/apis/{api_id}',['uses'=>'APIsController@read']);
    $router->get('/apis/{api_id}/versions/latest',['uses'=>'APIsController@latest_version']);
    $router->get('/apis/{api_id}/versions',['uses'=>'APIsController@versions']);
    $router->put('/apis/{api_id}',['uses'=>'APIsController@edit']);
    $router->put('/apis/{api_id}/publish',['uses'=>'APIsController@publish']);
    $router->put('/apis/{api_id}/code',['uses'=>'APIsController@code']);
    $router->post('/apis',['uses'=>'APIsController@add']);
    $router->delete('/apis/{api_id}',['uses'=>'APIsController@delete']);

    $router->get('/api_versions',['uses'=>'APIVersionsController@browse']);
    $router->get('/api_versions/{api_version_id}',['uses'=>'APIVersionsController@read']);
    $router->put('/api_versions/{api_version_id}',['uses'=>'APIVersionsController@edit']);
    $router->post('/api_versions',['uses'=>'APIVersionsController@add']);
    $router->delete('/api_versions/{api_version_id}',['uses'=>'APIVersionsController@delete']);
    
    $router->get('/api_instances',['uses'=>'APIInstancesController@browse']);
    $router->get('/api_instances/{api_instance_id}',['uses'=>'APIInstancesController@read']);
    $router->put('/api_instances/{api_instance_id}',['uses'=>'APIInstancesController@edit']);
    $router->post('/api_instances',['uses'=>'APIInstancesController@add']);
    $router->delete('/api_instances/{api_instance_id}',['uses'=>'APIInstancesController@delete']);
    
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

    $router->get('/api_docs/{api_instance_id}',['uses'=>'DocumentationController@fetch']);
});

$router->get('/{slug}', ['uses'=>'DocumentationController@docs']);
$router->get('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->post('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->put('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->delete('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);

