<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\ActivityLog;

class APIUser extends Model
{
  protected $fillable = ['app_name', 'app_secret', 'config','environment_id'];
  protected $casts = ['config' => 'object'];
  protected $table = 'api_users';

  public function setAppSecretAttribute($secret) {
    if ($secret !== '*****') {
      $this->attributes['app_secret'] = Hash::make($secret);
    }
  }

  public function getAppSecretAttribute($secret) {
    return '*****';
  }

  public function check_app_secret($secret) {
    return Hash::check($secret, $this->attributes['app_secret']);
  }

  public static function boot()
  {
    parent::boot();
    self::saved(function($model){
      dd(app('request')->method);
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


