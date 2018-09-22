<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

class Environment extends Model
{
  protected $fillable = ['domain', 'name' ,'type'];

  public function service_instances() {
    return $this->hasMany(ServiceInstance::class);
  }

  public static function boot()
  {
    parent::boot();
    self::saved(function($model){
      $activity_log = new ActivityLog([
        'data' => $model,
        'event' => snake_case(class_basename($model)),
      ]);
      $activity_log->save();
    });
  }


}