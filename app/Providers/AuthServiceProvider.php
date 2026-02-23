<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\API;
use App\Policies\APIPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

    protected $policies = [
        'App\API' => 'App\Policies\APIPolicy',
        'App\APIInstance'=>'App\Policies\APIInstancePolicy',
        'App\APIUser' => 'App\Policies\APIUserPolicy',
        'App\APIVersion' => 'App\Policies\APIPolicy',
        'App\User' => 'App\Policies\UserPolicy',
        'App\Environment' => 'App\Policies\EnvironmentPolicy',
        'App\Resource' => 'App\Policies\ResourcePolicy',
        'App\Scheduler' => 'App\Policies\SchedulerPolicy',
        'App\APIDeveloper' => 'App\Policies\APIDeveloperPolicy',
    ];
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        // Register policies manually
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
    }
}
