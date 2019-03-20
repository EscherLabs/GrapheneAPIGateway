<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

class Environment extends Model
{
  protected $fillable = ['domain', 'name' ,'type'];

  public function api_instances() {
    return $this->hasMany(APIInstance::class);
  }

  public static function boot()
  {
    parent::boot();
    self::saved(function($model){
      if (!app()->runningInConsole()) {
        $activity_log = new ActivityLog([
          'event' => class_basename($model),
          'new' => $model->getAttributes(),
          'old' => $model->getOriginal(),
        ]);
        $activity_log->save();
      }
    });
  }



}