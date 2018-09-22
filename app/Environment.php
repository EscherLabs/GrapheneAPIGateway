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
      if (!app()->runningInConsole()) {
        $activity_log = new ActivityLog([
          'event' => class_basename($model),
          'new' => $model,
          'old' => $model->getOriginal();
        ]);
        $activity_log->save();
      }
    });
  }



}