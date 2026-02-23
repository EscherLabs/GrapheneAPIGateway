<?php

namespace App\Policies;

use App\Models\User;
use App\Models\API;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->admin;
    }

    public function view(User $user)
    {

        return $user->admin;
    }

    public function manage(User $user)
    {
        return $user->admin;
    }

    public function delete(User $user, API $api)
    {
        return $user->admin;
    }
}
