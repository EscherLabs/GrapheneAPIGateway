<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
  protected $table = 'activity_log';

  protected $fillable = ['action','event','type','user_id','comment','data'];
  protected $casts = ['data' => 'object'];

  public static function boot()
  {
    parent::boot();
    self::saving(function($model){
        $model->user_id = '';
        $model->type = '';
        $model->comment = '';
        $model->action = request()->method;
        if (app('request')->has('user_id')) {
            $model->user_id = request()->input('user_id');
        }
        if (app('request')->has('type')) {
            $model->type = request()->input('type');
        }
        if (app('request')->has('summary')) {
            $model->comment = request()->input('summary');
        }
        if (app('request')->has('comment')) {
            $model->comment = request()->input('comment');
        }
    });
  }


}
