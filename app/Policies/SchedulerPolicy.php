<?php

namespace App\Policies;

use App\Models\User;
use App\Models\API;

class SchedulerPolicy
{
    public function viewAny(User $user)
    {
        return $user->admin || $user->developer || $user->is_api_developer();
    }
    public function view(User $user)
    {

        return $user->admin || $user->developer;
    }

    public function manage(User $user, API $api)
    {
        return $user->admin || $user->developer || $user->id == $api->user_id;
    }

    public function delete(User $user)
    {
        return $user->admin || $user->developer;
    }
}
