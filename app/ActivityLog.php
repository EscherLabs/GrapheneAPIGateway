<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Environment;

class ActivityLog extends Model
{
  protected $table = 'activity_log';

  protected $fillable = ['action','event','type','user_id','comment','new','old','event_id'];
  protected $casts = ['new' => 'object','old' => 'object'];

  public static function boot()
  {
    parent::boot();
    self::saving(function($model){
        $model->user_id = '';
        $model->type = '';
        $model->comment = '';
        $model->action = app('request')->method();
        if (app('request')->has('id')) {
            $model->event_id = app('request')->input('id');
        }
        if (app('request')->has('user_id')) {
            $model->user_id = app('request')->input('user_id');
        }
        if (app('request')->has('type')) {
            $model->type = app('request')->input('type');
        } else if (app('request')->has('environment_id')) {
            $environment = Environment::where('id',app('request')->environment_id)->first();
            if (!is_null($environment)) {
                $model->type = $environment->type;
            }
        }
        if ($model->type === 'dev') {
            $model->new = [];
            $model->old = [];
        }
        if (app('request')->has('comment')) {
            $model->comment = app('request')->input('comment');
        } else if (app('request')->has('summary')) {
            $model->comment = app('request')->input('summary');
        }
    });
  }


}
