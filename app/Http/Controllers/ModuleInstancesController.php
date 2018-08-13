<?php

namespace App\Http\Controllers;

use \App\ModuleInstance;

class ModuleInstancesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return ModuleInstance::all();
    }   
    public function read($module_instance_id) {
        return ModuleInstance::where('id',$module_instance_id)
            ->with('module')
            ->with('module_version')
            ->first();
    }   
}
