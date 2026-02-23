<?php

namespace App\Policies;

use App\Models\User;
use App\Models\API;

class ActivityLogPolicy
{
    public function viewAny(User $user)
    {
        return $user->admin;
    }
}
