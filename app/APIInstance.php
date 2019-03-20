<?php

namespace App;

use App\APIVersion;
use App\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class APIInstance extends Model
{
  protected $table = 'api_instances';
  protected $fillable = ['name','slug','public','api_version_id','environment_id','api_id','route_user_map','resources'];
  protected $casts = ['route_user_map' => 'object', 'resources' => 'object','public'=>'boolean'];

  public function api() {
    return $this->belongsTo(API::class);
  }
  public function api_version() {
    return $this->belongsTo(APIVersion::class);
  }
  public function environment() {
    return $this->belongsTo(Environment::class);
  }
  public function find_version() {
    $api_version = null;
    if(is_null($this->api_version_id)){
        $api_version = APIVersion::where('api_id','=',$this->api_id)->orderBy('created_at', 'desc')->first();
    }else if($this->api_version_id == 0){
        $api_version = APIVersion::where('api_id','=',$this->api_id)->where('stable','=',1)->orderBy('created_at', 'desc')->first();
    }else{
        $api_version = APIVersion::where('id','=',$this->api_version_id)->first();
    }
    return $api_version;
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