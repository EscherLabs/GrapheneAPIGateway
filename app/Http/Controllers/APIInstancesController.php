<?php

namespace App\Http\Controllers;

use \App\APIInstance;
use Illuminate\Http\Request;

class APIInstancesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return APIInstance::all();
    }   
    public function read($api_instance_id) {
        $api_instance =  APIInstance::where('id',$api_instance_id)
            ->with('api')
            ->with('api_version')
            ->first();
        if (!is_null($api_instance)) {
            return $api_instance;
        } else {
            return response('api_instance not found', 404);
        }
    }   

    public function edit(Request $request, $api_instance_id)
    {
        $api_instance = APIInstance::where('id',$api_instance_id)->first();
        if (!is_null($api_instance)) {
            $api_instance->update($request->all());
            return $api_instance;
        } else {
            return response('api_instance not found', 404);
        }
    }

    public function add(Request $request)
    {
        $api_instance = new APIInstance($request->all());
        $api_instance->save();
        return $api_instance;
    }

    public function delete($api_instance_id)
    {
        if ( APIInstance::where('id',$api_instance_id)->delete() ) {
            return [true];
        }
    }

}
