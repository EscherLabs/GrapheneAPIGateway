<?php

namespace App\Http\Controllers;

use \App\Database;

class DatabasesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Database::all();
    }   

    public function read($database_id)
    {
        $database = Database::where('id',$database_id)->first();
        if (!is_null($database)) {
            return $database;
        } else {
            return response('database not found', 404);
        }
    }

    public function edit(Request $request, $database_id)
    {
        $database = Database::where('id',$database_id)->first();
        $database->update($request->all());
        return $database;
    }

    public function add(Request $request)
    {
        $database = new Database($request->all());
        $database->save();
        return $database;
    }

    public function delete($database_id)
    {
        if ( Database::where('id',$database_id)->delete() ) {
            return [true];
        }
    }

}
