<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
  protected $fillable = ['name'];

  public function database_instances() {
    return $this->hasMany(DatabaseInstance::class);
  }

}