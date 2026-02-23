<?php

namespace App\Policies;

use App\Models\APIDeveloper;
use App\Models\User;
use App\Models\API;

class APIUserPolicy
{
    public function viewAny(User $user)
    {
        return $user->admin || $user->developer || $user->is_api_developer();
    }

    public function view(User $user)
    {
        return $user->admin || $user->developer;
    }

    public function manage(User $user)
    {
        return $user->admin || $user->developer;
    }

    public function delete(User $user)
    {
        return $user->admin;
    }

    public function getDecryptedSecret(User $user)
    {
        return $user->admin || $user->developer;
    }
}
