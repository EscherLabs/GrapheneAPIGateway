<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class Authorize
{
    protected $bindings = [
        'api_id' => \App\Models\API::class,
        'api_instance_id' => \App\Models\APIInstance::class,
        'api_version_id' => \App\Models\APIVersion::class,
        'api_user_id' => \App\Models\APIUser::class,
        'environment_id' => \App\Models\Environment::class,
        'resource_id' => \App\Models\Resource::class,
        'scheduler_id' => \App\Models\Scheduler::class,
        'user_id' => \App\Models\User::class
    ];

    public function handle($request, Closure $next, $ability, $param = null)
    {
        $routeParams = $request->route()[2] ?? [];
        $model = null;
        // Lumen route params are in index 2
        if ($param && isset($routeParams[$param]) && isset($this->bindings[$param])) {
            $modelClass = $this->bindings[$param];
            $model = $modelClass::find($routeParams[$param]);
            if (!$model) return response('Not found', 404);
        }
        elseif (class_exists($param)) {
            $model = $param;
        }

        // Authorize using the Gate
        Gate::authorize($ability, $model);

        return $next($request);
    }
}

