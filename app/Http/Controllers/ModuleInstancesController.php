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
        $module_instance =  ModuleInstance::where('id',$module_instance_id)
            ->with('module')
            ->with('module_version')
            ->first();
        if (!is_null($module_instance)) {
            return $module_instance;
        } else {
            response('module_instance not found', 404);
        }
    }   

    public function edit(Request $request, $module_instance_id)
    {
        $module_instance = ModuleInstance::where('id',$module_instance_id)->first();
        $module_instance->update($request->all());
        return $module_instance;
    }

    public function add(Request $request)
    {
        $module_instance = new ModuleInstance($request->all());
        $module_instance->save();
        return $module_instance;
    }

    public function delete($module_instance_id)
    {
        if ( ModuleInstance::where('id',$module_instance_id)->delete() ) {
            return [true];
        }
    }

}
