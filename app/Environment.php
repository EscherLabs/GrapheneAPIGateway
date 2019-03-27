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
        $orig = $model->getOriginal();
        foreach($orig as $attr => $attr_val) {
          if (isset($model->casts[$attr]) && $model->casts[$attr] === 'object') {
            $orig[$attr] = json_decode($attr_val);
          }
        }
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