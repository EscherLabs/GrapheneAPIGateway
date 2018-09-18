<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceInstance extends Model
{
  protected $fillable = ['name','slug','public','service_version_id','environment_id','service_id','route_user_map','resources'];
  protected $casts = ['route_user_map' => 'object', 'resources' => 'object','public'=>'boolean'];

  public function service() {
    return $this->belongsTo(Service::class);
  }
  public function service_version() {
    return $this->belongsTo(ServiceVersion::class);
  }
  public function environment() {
    return $this->belongsTo(Environment::class);
  }

}