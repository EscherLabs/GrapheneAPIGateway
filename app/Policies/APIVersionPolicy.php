<?php

namespace App\Policies;

use App\Models\API;
use App\Models\APIDeveloper;
use App\Models\User;
use App\Models\APIVersion;

class APIVersionPolicy
{
    public function viewAny(User $user)
    {
        return $user->admin || $user->developer || $user->is_api_developer();
    }

    public function view_manage(User $user, APIVersion $api_version)
    {
        $api = API::where('id',$api_version->api_id)->first();
        $is_api_developer = false;
        if ($api){
            $is_api_developer = APIDeveloper::where('user_id',$user->id)->where('api_id',$api->id)->exists();
        }
        return  $user->id == $api->user_id || $is_api_developer;
    }

}
