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
    $router->get('/api_users',['uses'=>'APIUsersController@browse',
        'middleware' => 'can:viewAny,App\Models\APIUser'
    ]);
    $router->get('/api_users/{apiuser_id}',['uses'=>'APIUsersController@read', 'middleware' => 'can:view,App\Models\APIUser']);
    $router->get('/api_users/{apiuser_id}/decrypted_secret',['uses'=>'APIUsersController@decrypted_app_secret' ,'middleware' => 'can:getDecryptedSecret,App\Models\APIUser']);
    $router->put('/api_users/{apiuser_id}',['uses'=>'APIUsersController@edit','middleware' => 'can:manage,App\Models\APIUser']);
    $router->post('/api_users',['uses'=>'APIUsersController@add', 'middleware' => 'can:manage,App\Models\APIUser']);
    $router->delete('/api_users/{apiuser_id}',['uses'=>'APIUsersController@delete', 'middleware' => 'can:delete,App\Models\APIUser']);

    $router->get('/environments',['uses'=>'EnvironmentsController@browse','middleware' => 'can:viewAny,App\Models\Environment']);
    $router->get('/environments/{environment_id}',['uses'=>'EnvironmentsController@read','middleware' => 'can:view,environment_id']);
    $router->put('/environments/{environment_id}',['uses'=>'EnvironmentsController@edit','middleware' => 'can:manage,environment_id']);
    $router->post('/environments',['uses'=>'EnvironmentsController@add','middleware' => 'can:create,App\Models\Environment']);
    $router->delete('/environments/{environment_id}',['uses'=>'EnvironmentsController@delete','middleware' => 'can:manage,environment_id']);
    $router->get('/environments/{environment_id}/api_users',['uses'=>'EnvironmentsController@api_users','middleware' => 'can:viewAny,App\Models\APIUser']);

    $router->get('/apis',['uses'=>'APIsController@browse', 'middleware' => 'can:viewAny,App\Models\API']);
    $router->get('/apis/{api_id}',['uses'=>'APIsController@read', 'middleware' => 'can:view,api_id']);
    $router->get('/apis/{api_id}/versions/latest',['uses'=>'APIsController@latest_version','middleware' => 'can:view,api_id']);
    $router->get('/apis/{api_id}/versions',['uses'=>'APIsController@versions','middleware' => 'can:view,api_id']);
    $router->put('/apis/{api_id}',['uses'=>'APIsController@edit','middleware' => 'can:manage,api_id']);
    $router->put('/apis/{api_id}/publish',['uses'=>'APIsController@publish','middleware' => 'can:manage,api_id']);
    $router->put('/apis/{api_id}/code',['uses'=>'APIsController@code','middleware' => 'can:manage,api_id']);
    $router->post('/apis',['uses'=>'APIsController@add','middleware' => 'can:create,App\Models\API']);
    $router->delete('/apis/{api_id}',['uses'=>'APIsController@delete','middleware' => 'can:manage,api_id']);

    $router->get('/apis/{api_id}/developers',['uses'=>'APIsController@browseDevelopers','middleware' => 'can:viewAny,App\Models\API']);
    $router->post('/apis/{api_id}/developers/{user_id}',['uses'=>'APIsController@addDeveloper','middleware' => 'can:manage,api_id']);
    $router->delete('/apis/{api_id}/developers/{user_id}',['uses'=>'APIsController@deleteDeveloper','middleware' => 'can:delete,api_id']);

    $router->get('/api_versions',['uses'=>'APIVersionsController@browse','middleware' => 'can:viewAny,App\Models\APIVersion']);
    $router->get('/api_versions/{api_version_id}',['uses'=>'APIVersionsController@read','middleware' => 'can:view,api_version_id']);
    $router->put('/api_versions/{api_version_id}',['uses'=>'APIVersionsController@edit','middleware' => 'can:view,api_version_id']);
    $router->post('/api_versions',['uses'=>'APIVersionsController@add']);
    $router->delete('/api_versions/{api_version_id}',['uses'=>'APIVersionsController@delete','middleware' => 'can:manage,api_version_id']);
    
    $router->get('/api_instances',['uses'=>'APIInstancesController@browse','middleware' => 'can:viewAny,App\Models\APIInstance']);
    $router->get('/api_instances/{api_instance_id}',['uses'=>'APIInstancesController@read','middleware' => 'can:view,api_instance_id']);
    $router->put('/api_instances/{api_instance_id}',['uses'=>'APIInstancesController@edit','middleware' => 'can:manage,api_instance_id']);
    $router->post('/api_instances',['uses'=>'APIInstancesController@add','middleware' => 'can:create,App\Models\APIInstance']);
    $router->delete('/api_instances/{api_instance_id}',['uses'=>'APIInstancesController@delete','middleware' => 'can:manage,api_instance_id']);
    
    $router->get('/resources',['uses'=>'ResourcesController@browse','middleware' => 'can:viewAny,App\Models\Resource']);
    $router->get('/resources/type/{type}',['uses'=>'ResourcesController@browse_by_type','middleware' => 'can:viewAny,App\Models\Resource']);
    $router->get('/resources/{resource_id}',['uses'=>'ResourcesController@read','middleware' => 'can:view,resource_id']);
    $router->put('/resources/{resource_id}',['uses'=>'ResourcesController@edit','middleware' => 'can:manage,resource_id']);
    $router->post('/resources',['uses'=>'ResourcesController@add','middleware' => 'can:create,resource_id']);
    $router->delete('/resources/{resource_id}',['uses'=>'ResourcesController@delete','middleware' => 'can:delete,resource_id']);

    $router->get('/scheduler',['uses'=>'SchedulerController@browse','middleware' => 'can:viewAny,App\Models\Scheduler']);
    $router->get('/scheduler/{scheduler_id}',['uses'=>'SchedulerController@read','middleware' => 'can:view,scheduler_id']);
    $router->get('/scheduler/{scheduler_id}/run',['uses'=>'SchedulerController@run','middleware' => 'can:manage,scheduler_id']);
    $router->put('/scheduler/{scheduler_id}',['uses'=>'SchedulerController@edit','middleware' => 'can:manage,scheduler_id']);
    $router->post('/scheduler',['uses'=>'SchedulerController@add','middleware' => 'can:create,scheduler_id']);
    $router->delete('/scheduler/{scheduler_id}',['uses'=>'SchedulerController@delete','middleware' => 'can:delete,scheduler_id']);

    $router->get('/users',['uses'=>'UsersController@browse','middleware' => 'can:viewAny,App\Models\User']);
    $router->get('/users/{user_id}',['uses'=>'UsersController@read','middleware' => 'can:view,user_id']);
    $router->put('/users/{user_id}',['uses'=>'UsersController@edit','middleware' => 'can:manage,user_id']);
    $router->post('/users',['uses'=>'UsersController@add','middleware' => 'can:create,user_id']);
    $router->delete('/users/{user_id}',['uses'=>'UsersController@delete','middleware' => 'can:delete,user_id']);

    $router->get('/activity_log',['uses'=>'ActivityLogController@browse','middleware' => 'can:viewAny,App\Models\ActivityLog']);

    $router->get('/api_docs/{api_instance_id}',['uses'=>'DocumentationController@fetch']);
});

$router->get('/{slug}', ['uses'=>'DocumentationController@docs']);
$router->get('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->post('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->put('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);
$router->delete('/{slug}{any:.*}', ['uses'=>'ExecController@exec']);

