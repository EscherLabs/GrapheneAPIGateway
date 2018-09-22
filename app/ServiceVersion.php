<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

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