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
        return DatabaseInstance::where('id',$database_instance_id)
            ->with('database')
            ->first();
    }   
}
