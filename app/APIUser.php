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
        $orig = $model->getOriginal();
        foreach($orig as $attr => $attr_val) {
          if (isset($model->casts[$attr]) && $model->casts[$attr] === 'object') {
            $orig[$attr] = json_decode($attr_val);
          }
        }
        if (isset($orig['app_secret']) && $orig['app_secret'] !== '') {$orig['app_secret'] = '*****';}
        $activity_log = new ActivityLog([
          'event' => class_basename($model),
          'new' => $model,
          'old' => $orig,
        ]);
        $activity_log->save();
      }
    });
  }

}


