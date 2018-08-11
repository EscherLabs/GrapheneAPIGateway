<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Scenario extends Model
{
    protected $fillable = ['name', 'scenario'];
    protected $casts = ['scenario' => 'object'];

    public function team_members() {
      return $this->hasMany(TeamMember::class);
    }
}
