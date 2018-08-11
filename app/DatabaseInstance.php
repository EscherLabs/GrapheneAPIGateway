<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatabaseInstance extends Model
{
  protected $fillable = ['name','database_id','config'];
  protected $casts = ['config' => 'object'];

  public function database() {
    return $this->belongsTo(Database::class);
  }

}
