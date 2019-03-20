<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

class API extends Model
{
  protected $table = 'apis';

  protected $fillable = ['name', 'description', 'tags' ,'user_id'];

  public function api_instances() {
    return $this->hasMany(APIInstance::class);
  }

  public function api_versions()
  {
    return $this->hasMany(APIVersion::class);
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