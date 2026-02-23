<?php

namespace App\Policies;

use App\Models\APIDeveloper;
use App\Models\User;
use App\Models\API;

class APIPolicy
{
    public function viewAny(User $user){
        $api_developer = APIDeveloper::where('user_id',$user->id)->exists();
        return $user->admin || $user->developer || $api_developer;
    }

    public function view(User $user, API $api)
    {
        $api_developer = APIDeveloper::where('api_id',$api->id)->exists();
        return $user->admin || $user->developer || $user->id == $api->user_id || $api_developer;
    }

    public function manage(User $user, API $api)
    {
        $api_developer = APIDeveloper::where('api_id',$api->id)->exists();
        return $user->id == $api->user_id || $api_developer;
    }

    public function create(User $user)
    {
        return $user->admin || $user->developer;
    }

    public function delete(User $user, API $api)
    {
        return $user->admin || $user->id == $api->user_id;
    }

    public function manage_developers(User $user, API $api){
        $api_developer = APIDeveloper::where('api_id',$api->id)->exists();
        return $user->admin || $user->developers || $user->id == $api->user_id || $api_developer;
    }
}
