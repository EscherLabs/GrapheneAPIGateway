<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ActivityLog;
use Illuminate\Support\Facades\Crypt;

class Resource extends Model
{
  protected $fillable = ['name','type','config','resource_type'];
  protected $secret_fields = ['password','pass','secret','key'];

  private function getConfigHelper($config_string,$return_secrets=false)
  {
    if (!is_null($config_string) && $config_string != '') {
      $resource_config = json_decode($config_string,true);
      if (is_array($resource_config)) {
        foreach($resource_config as $index => $config_attr) {
          foreach($this->secret_fields as $secret_field) {
            if ($index === $secret_field || $this->resource_type === $secret_field) {
              if ($return_secrets === true) {
                try {
                  $resource_config[$index] = Crypt::decrypt($resource_config[$index]);
                } catch (\Exception $e) {
                  $resource_config[$index] = $resource_config[$index];
                }
              } else {
                $resource_config[$index] = '*****';
              }
            }
          }
        }
        return (object)$resource_config;
      } else {
        return (object)[];
      }
    } else {
      return null;
    }
  }

  public function getConfigAttribute($config_string)
  {
    return $this->getConfigHelper($config_string,false);
  }

  public function getConfigWithSecretsAttribute($config_string)
  {
    return $this->getConfigHelper($this->attributes['config'],true);
  }

  public function setConfigAttribute($new_config)
  {
    $orig_config = [];
    if (isset($this->attributes['config']) && is_array($this->attributes['config'])) {
      $orig_config = json_decode($this->attributes['config'],true);
    }
    foreach($new_config as $index => $new_config_attr) {
      foreach($this->secret_fields as $secret_field) {
        if (($index === $secret_field || $this->resource_type === $secret_field) && $new_config_attr !== '*****' && $new_config_attr !== '') {
          $new_config[$index] = Crypt::encrypt($new_config_attr);
        } else if (($index === $secret_field || $this->resource_type === $secret_field) && $new_config_attr === '*****' && isset($orig_config[$index])) {
          $new_config[$index] = $orig_config[$index];
        }
      }
    }
    $this->attributes['config'] = json_encode($new_config);
  }

  public static function boot()
  {
    parent::boot();
    self::saved(function($model){
      if (!app()->runningInConsole()) {
        $activity_log = new ActivityLog([
          'event' => class_basename($model),
          'new' => $model,
          'old' => $model->getOriginal(),
        ]);
        $activity_log->save();
      }
    });
  }



}