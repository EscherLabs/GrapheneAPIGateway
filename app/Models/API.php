<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLog;

class API extends Model
{
  protected $table = 'apis';

  protected $fillable = ['api_type','name', 'description', 'tags' ,'user_id'];

  public function api_instances() {
    return $this->hasMany(APIInstance::class);
  }

  public function api_versions()
  {
    return $this->hasMany(APIVersion::class);
  }
  public function user(){
      return $this->belongsTo(User::class, 'user_id');
  }

  public function developers(){
      return $this->belongsToMany(User::class,'api_developers',
          'api_id',   // foreign key on pivot referencing APIs table
          'user_id');
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