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

  public function environment() {
    return $this->belongsTo(Environment::class);
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
      if (!app()->runningInConsole()) {
        $activity_log = new ActivityLog([
          'event' => class_basename($model),
          'new' => $model,
          'old' => $model->getOriginal(),
        ]);
        $activity_log->save();
      }
    });
  }


}


