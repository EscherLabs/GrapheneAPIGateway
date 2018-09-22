<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;

class Resource extends Model
{
  protected $fillable = ['name','type','config','resource_type'];
  protected $casts = ['config' => 'object'];

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