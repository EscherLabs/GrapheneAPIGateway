<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
  protected $fillable = ['name', 'description', 'tags' ,'user_id'];

  public function service_instances() {
    return $this->hasMany(ServiceInstance::class);
  }

  public function service_versions()
  {
    return $this->hasMany(ServiceVersion::class);
  }

}