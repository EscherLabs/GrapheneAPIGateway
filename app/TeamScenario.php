<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TeamScenario extends Model
{
    protected $fillable = ['team_id','scenario','user_id'];
    protected $casts = ['scenario' => 'object'];

    public function team() {
      return $this->belongsTo(TeamMember::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

}