<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedular extends Model
{
  protected $fillable = ['cron','service_instance_id','route'];
  protected $casts = ['params' => 'object'];

}