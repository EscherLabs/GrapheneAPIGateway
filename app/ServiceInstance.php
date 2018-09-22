<?php

namespace App;

use App\ServiceVersion;
use App\ActivityLog;
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
  public function find_version() {
    $service_version = null;
    if(is_null($this->service_version_id)){
        $service_version = ServiceVersion::where('service_id','=',$this->service_id)->orderBy('created_at', 'desc')->first();
    }else if($this->service_version_id == 0){
        $service_version = ServiceVersion::where('service_id','=',$this->service_id)->where('stable','=',1)->orderBy('created_at', 'desc')->first();
    }else{
        $service_version = ServiceVersion::where('id','=',$this->service_version_id)->first();
    }
    return $service_version;
  }

  public static function boot()
  {
    parent::boot();
    self::updated(function($model){
      if (!is_null(app('request')->method)) {
        $activity_log = new ActivityLog([
          'data' => $model,
          'event' => class_basename($model),
        ]);
        $activity_log->save();
      }
    });
  }


}