<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
  protected $fillable = ['name', 'description', 'tags' ,'user_id'];

  public function module_instances() {
    return $this->hasMany(ModuleInstance::class);
  }

  public function module_versions()
  {
    return $this->hasMany(ModuleVersion::class);
  }

}