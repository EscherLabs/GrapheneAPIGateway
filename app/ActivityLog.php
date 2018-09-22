<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Environment;

class ActivityLog extends Model
{
  protected $table = 'activity_log';

  protected $fillable = ['action','event','type','user_id','comment','data'];
  protected $casts = ['data' => 'object'];

  public static function boot()
  {
    parent::boot();
    self::saving(function($model){
        $request_params = app('request')->all();
        $model->user_id = '';
        $model->type = '';
        $model->comment = '';
        $model->action = app('request')->method();
        if (app('request')->has('user_id')) {
            $model->user_id = app('request')->input('user_id');
            unset($request_params['user_id']);
        }
        if (app('request')->has('type')) {
            $model->type = app('request')->input('type');
            unset($request_params['type']);
        } else if (app('request')->has('environment_id')) {
            $environment = Environment::where('id',app('request')->environment_id)->first();
            if (!is_null($environment)) {
                $model->type = $environment->type;
            }
        }
        if (app('request')->has('comment')) {
            $model->comment = app('request')->input('comment');
            unset($request_params['comment']);
        } else if (app('request')->has('summary')) {
            $model->comment = app('request')->input('summary');
            unset($request_params['summary']);
        }
        $model->data = $request_params;
    });
  }


}
