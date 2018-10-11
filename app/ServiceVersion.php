<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

class ServiceVersion extends Model
{

  protected $fillable = ['service_id', 'summary', 'description', 'stable' ,'files', 'functions', 'resources', 'routes', 'user_id'];
  protected $casts = ['files' => 'object', 'functions'=> 'object','resources' => 'object', 'routes' => 'object'];


  public function service() {
    return $this->belongsTo(Service::class);
  }
  public function service_instances() {
    return $this->hasOne(ServiceInstance::class);
  }  

  // public static function boot()
  // {
  //   parent::boot();
  //   self::saved(function($model){
  //     if (!app()->runningInConsole()) {
  //       $activity_log = new ActivityLog([
  //         'event' => class_basename($model),
  //         'data' => $model,
  //       ]);
  //       $activity_log->save();
  //     }
  //   });
  // }

}