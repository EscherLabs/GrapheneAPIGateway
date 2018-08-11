<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Environment extends Model
{
  protected $fillable = ['domain', 'name'];

  public function module_instances() {
    return $this->hasMany(ModuleInstance::class);
  }

}