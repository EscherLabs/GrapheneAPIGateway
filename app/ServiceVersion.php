<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceVersion extends Model
{

  protected $fillable = ['service_id', 'summary', 'description', 'stable' ,'code', 'resources', 'routes', 'user_id'];
  protected $casts = ['code' => 'object', 'resources' => 'object', 'routes' => 'object'];


  public function service() {
    return $this->belongsTo(Service::class);
  }
  public function service_instances() {
    return $this->hasOne(ServiceInstance::class);
  }  


}