<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleVersion extends Model
{

  protected $fillable = ['module_id', 'summary', 'description', 'stable' ,'code', 'databases', 'routes', 'user_id'];
  protected $casts = ['code' => 'object', 'databases' => 'object', 'routes' => 'object'];


  public function module() {
    return $this->belongsTo(Module::class);
  }
  public function module_instances() {
    return $this->hasOne(ModuleInstance::class);
  }  


}