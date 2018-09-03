<?php

namespace App\Http\Controllers;

use \App\DatabaseInstance;

class DatabaseInstancesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return DatabaseInstance::all();
    }   
    public function read($database_instance_id) {
        $database_instance  = DatabaseInstance::where('id',$database_instance_id)
            ->with('database')
            ->first();
        if (!is_null($database_instance)) {
            return $database_instance;
        } else {
            response('database_instance not found', 404);
        }
    }  

    public function edit(Request $request, $database_instance_id)
    {
        $database_instance = DatabaseInstance::where('id',$database_instance_id)->first();
        $database_instance->update($request->all());
        return $database_instance;
    }

    public function add(Request $request)
    {
        $database_instance = new DatabaseInstance($request->all());
        $database_instance->save();
        return $database_instance;
    }

    public function delete($database_instance_id)
    {
        if ( DatabaseInstance::where('id',$database_instance_id)->delete() ) {
            return [true];
        }
    }
}
