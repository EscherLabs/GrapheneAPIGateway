<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cron\CronExpression;

class Scheduler extends Model
{
  protected $table = 'scheduler';

  protected $fillable = ['cron','service_instance_id','route','name','args'];
  protected $casts = ['args' => 'object','last_response'=>'object'];
  protected $appends = ['next_run'];

  public function getNextRunAttribute() {
    $cron = CronExpression::factory($this->attributes['cron']);
    return $cron->getNextRunDate()->format('Y-m-d H:i:s');
  }

}