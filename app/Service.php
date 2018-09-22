<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

class Service extends Model
{
  protected $fillable = ['name', 'description', 'tags' ,'user_id'];

  public function service_instances() {
    return $this->hasMany(ServiceInstance::class);
  }

  public function service_versions()
  {
    return $this->hasMany(ServiceVersion::class);
  }

  public static function boot()
  {
    parent::boot();
    self::saved(function($model){
      if (!is_null(app('request')->method())) {
        $activity_log = new ActivityLog([
          'data' => $model,
          'event' => class_basename($model),
        ]);
        $activity_log->save();
      }
    });
  }



}