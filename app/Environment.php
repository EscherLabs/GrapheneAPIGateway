<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Environment extends Model
{
  protected $fillable = ['domain', 'name'];

  public function service_instances() {
    return $this->hasMany(ServiceInstance::class);
  }

}