<?php

namespace App\Http\Controllers;

use \App\ModuleVersion;

class ModuleVersionsController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return ModuleVersion::all();
    }   

    public function read($module_version_id)
    {
        $module_version = ModuleVersion::where('id',$module_version_id)->first();
        if (!is_null($module_version)) {
            return $module_version;
        } else {
            response('module_version not found', 404);
        }
    }

    public function edit(Request $request, $module_version_id)
    {
        $module_version = ModuleVersion::where('id',$module_version_id)->first();
        $module_version->update($request->all());
        return $module_version;
    }

    public function add(Request $request)
    {
        $module_version = new ModuleVersion($request->all());
        $module_version->save();
        return $module_version;
    }

    public function delete($module_version_id)
    {
        if ( ModuleVersion::where('id',$module_version_id)->delete() ) {
            return [true];
        }
    }

}
