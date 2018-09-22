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
      $activity_log = new ActivityLog([
        'data' => $model,
        'event' => snake_case(class_basename($model)),
      ]);
      $activity_log->save();
    });
  }


}