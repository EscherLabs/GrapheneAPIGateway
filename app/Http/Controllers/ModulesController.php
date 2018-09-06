<?php

namespace App\Http\Controllers;

use \App\Module;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Module::all();
    }   

    public function read($module_id)
    {
        $module = Module::where('id',$module_id)->first();
        if (!is_null($module)) {
            return $module;
        } else {
            return response('module not found', 404);
        }
    }

    public function edit(Request $request, $module_id)
    {
        $module = Module::where('id',$module_id)->first();
        if (!is_null($module)) {
            $module->update($request->all());
            return $module;
        } else {
            return response('module not found', 404);
        }
    }

    public function add(Request $request)
    {
        $module = new Module($request->all());
        $module->save();
        return $module;
    }

    public function delete($module_id)
    {
        if ( Module::where('id',$module_id)->delete() ) {
            return [true];
        }
    }

}
