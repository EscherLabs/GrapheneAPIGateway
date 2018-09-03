<?php

namespace App\Http\Controllers;

use \App\Environment;

class EnvironmentsController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Environment::all();
    }   

    public function read($environment_id)
    {
        $environment = Environment::where('id',$environment_id)->first();
        if (!is_null($environment)) {
            return $environment;
        } else {
            response('database not found', 404);
        }
    }

    public function edit(Request $request, $environment_id)
    {
        $environment = Environment::where('id',$environment_id)->first();
        $environment->update($request->all());
        return $environment;
    }

    public function add(Request $request)
    {
        $environment = new Environment($request->all());
        $environment->save();
        return $environment;
    }

    public function delete($environment_id)
    {
        if ( Environment::where('id',$environment_id)->delete() ) {
            return [true];
        }
    }

}
