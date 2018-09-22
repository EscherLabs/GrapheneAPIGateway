<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cron\CronExpression;
use App\ActivityLog;

class Scheduler extends Model
{
  protected $table = 'scheduler';

  protected $fillable = ['cron','service_instance_id','route','name','args','type'];
  protected $casts = ['args' => 'object','last_response'=>'object'];
  protected $appends = ['next_runtimes'];

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
        $activity_log = new ActivityLog([
          'event' => class_basename($model),
        ]);
        $activity_log->save();
      }
    });
  }



}