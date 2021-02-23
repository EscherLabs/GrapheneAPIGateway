<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cron\CronExpression;
use App\ActivityLog;

class Scheduler extends Model
{
  protected $table = 'scheduler';

  protected $fillable = ['cron','api_instance_id','route','name','args','enabled'];
  protected $casts = ['args' => 'object','last_response'=>'object','enabled'=>'boolean'];
  protected $appends = ['next_runtimes'];

  public function api_instance() {
    return $this->belongsTo(APIInstance::class);
  }

  public function getNextRuntimesAttribute() {
    try {
      $cron = CronExpression::factory($this->attributes['cron']);
      return [
        $cron->getNextRunDate(null,0)->format('Y-m-d H:i:s'),
        $cron->getNextRunDate(null,1)->format('Y-m-d H:i:s'),
        $cron->getNextRunDate(null,2)->format('Y-m-d H:i:s'),
        $cron->getNextRunDate(null,3)->format('Y-m-d H:i:s'),
        $cron->getNextRunDate(null,4)->format('Y-m-d H:i:s'),
      ];
    } catch (\Exception $e) {
      return [];
    }
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