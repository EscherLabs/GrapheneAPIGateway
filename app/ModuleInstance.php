<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleInstance extends Model
{
  protected $fillable = ['name','slug','public','module_version_id','environment_id','module_id','route_user_map','database_instance_map'];
  protected $casts = ['route_user_map' => 'object', 'database_instance_map' => 'object','public'=>'boolean'];

  public function module() {
    return $this->belongsTo(Module::class);
  }
  public function module_version() {
    return $this->belongsTo(ModuleVersion::class);
  }
  public function environment() {
    return $this->belongsTo(Environment::class);
  }

}