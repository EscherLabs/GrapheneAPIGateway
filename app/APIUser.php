<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class APIUser extends Model
{
  protected $fillable = ['app_name', 'app_secret', 'config'];
  protected $casts = ['config' => 'object'];

  public function setAppSecretAttribute($secret) {
    if ($secret !== '*****') {
      $this->attributes['app_secret'] = Hash::make($secret);
    }
  }

  public function getAppSecretAttribute($secret) {
    return '*****';
  }

  public function check_app_secret($secret) {
    return Hash::check($secret, $this->attributes['app_secret']);
  }

}


