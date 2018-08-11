<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Closure;

class PublicAPIAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $logged_in = false;
        if ($request->header('PHP_AUTH_USER', null) && $request->header('PHP_AUTH_PW', null)) {
            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
            // $user = APIUser::where('site_id',config('app.site')->id)->
            //     where('app_name','=',$username)->first();
            if ($username === 'pharmacyemr' && $password === 'pharmacyemr') {
                $logged_in = true;
            }
        }
    
        if ($logged_in === false) {
            $headers = ['WWW-Authenticate' => 'Basic'];
            return response()->make('Invalid credentials.', 401, $headers);
        } else {
            return $next($request);
        }    
    }
}
