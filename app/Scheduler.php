<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
  protected $table = 'scheduler';

  protected $fillable = ['cron','service_instance_id','route','name','args'];
  protected $casts = ['args' => 'object'];

}