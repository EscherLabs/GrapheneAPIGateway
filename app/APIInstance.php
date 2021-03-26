<?php

namespace App;

use App\APIVersion;
use App\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class APIInstance extends Model
{
  protected $table = 'api_instances';
  protected $fillable = ['name','slug','public','api_version_id','environment_id','api_id','route_user_map','resources','errors'];
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
  public function find_version_id() {
    $api_version_id = null;
    if(is_null($this->api_version_id)){
        $api_version_id = APIVersion::select('id')
            ->where('api_id',$this->api_id)
            ->orderby('created_at','desc')->first();
    } else if($this->api_version_id == 0) {
        $api_version_id = APIVersion::select('id')
            ->where('api_id',$this->api_id)
            ->where('stable','=',1)
            ->orderby('created_at','desc')->first();
    } else {
        $api_version_id = APIVersion::select('id')->where('id','=',$this->api_version_id)->first();
    }
    return $api_version_id->id;
  }
  public function find_version() {
      $latest_version = $this->find_version_id();
      return APIVersion::where('id','=',$latest_version)->orderBy('created_at', 'desc')->first();
  }
  public static function boot()
  {
    parent::boot();
    self::saved(function($model){
      if (!app()->runningInConsole()) {
        $orig = $model->getOriginal();
        // foreach($orig as $attr => $attr_val) {
        //   if (isset($model->casts[$attr]) && $model->casts[$attr] === 'object') {
        //     $orig[$attr] = json_decode($attr_val);
        //   }
        // }
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