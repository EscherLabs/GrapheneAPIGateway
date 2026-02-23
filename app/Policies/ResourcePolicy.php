<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Resource;

class ResourcePolicy
{
    public function viewAny(User $user)
    {
        return $user->admin || $user->developer || $user->is_api_developer();
    }

    public function view(User $user)
    {
        return $user->admin;
    }

    public function manage(User $user)
    {
        return $user->admin;
    }

    public function delete(User $user)
    {
        return $user->admin;
    }
}
