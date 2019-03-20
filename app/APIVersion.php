<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

class APIVersion extends Model
{
  protected $table = 'api_versions';
  protected $fillable = ['api_id', 'summary', 'description', 'stable' ,'files', 'functions', 'resources', 'routes', 'user_id'];
  protected $casts = ['files' => 'object', 'functions'=> 'object','resources' => 'object', 'routes' => 'object'];


  public function api() {
    return $this->belongsTo(API::class);
  }
  public function api_instances() {
    return $this->hasOne(APIInstance::class);
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