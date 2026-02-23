<?php

namespace App\Http\Middleware;
use App\Models\User;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;


class HeaderUserAuth
{
    public function handle(Request $request, Closure $next)
    {
        $userId = $request->header('X-Unique-Id');

        if ($userId) {
            $user = User::where('unique_id', $userId)->first();
            if ($user && $user->active) {
                Auth::setUser($user);
            }
        }

        return $next($request);
    }
}