<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = ['first_name', 'last_name', 'email', 'unique_id'];

    public function team_members() {
        return $this->hasMany(TeamMember::class);
    }

    public function roles() {
        return $this->hasMany(Role::class);
    }
}
