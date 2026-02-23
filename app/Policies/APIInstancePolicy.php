<?php

namespace App\Policies;

use App\Models\APIDeveloper;
use App\Models\APIInstance;
use App\Models\User;
use App\Models\API;

class APIInstancePolicy
{
    public function viewAny(User $user)
    {
        return $user->admin || $user->developer || $user->is_api_developer();
    }

    public function view(User $user, APIInstance $api_instance_id)
    {
        $api = API::where('id',$api_instance_id->api_id)->first();
        $is_api_developer = APIDeveloper::where('api_id',$api_instance_id->api_id)->where('user_id',$user->id)->exists();
        return $user->admin || $user->id == $api->user_id || $is_api_developer;
    }
    public function create(User $user)
    {
        $api_owner = API::where('user_id',$user->id)->exists();
        return $user->admin || $api_owner || $user->developer;
    }
    public function manage(User $user, APIInstance $api_instance_id)
    {
        $api = API::where('id',$api_instance_id->api_id)->first();
        $api_developer = APIDeveloper::where('user_id',$user->id)->where('api_id',$api->id)->exists();

        return $user->id == $api->user_id || $api_developer;
    }

    public function delete(User $user)
    {
        return $user->admin;
    }
}
