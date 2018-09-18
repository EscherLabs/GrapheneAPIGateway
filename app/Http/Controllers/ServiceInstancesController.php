<?php

namespace App\Http\Controllers;

use \App\ServiceInstance;
use Illuminate\Http\Request;

class ServiceInstancesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return ServiceInstance::all();
    }   
    public function read($service_instance_id) {
        $service_instance =  ServiceInstance::where('id',$service_instance_id)
            ->with('service')
            ->with('service_version')
            ->first();
        if (!is_null($service_instance)) {
            return $service_instance;
        } else {
            return response('service_instance not found', 404);
        }
    }   

    public function edit(Request $request, $service_instance_id)
    {
        $service_instance = ServiceInstance::where('id',$service_instance_id)->first();
        if (!is_null($service_instance)) {
            $service_instance->update($request->all());
            return $service_instance;
        } else {
            return response('service_instance not found', 404);
        }
    }

    public function add(Request $request)
    {
        $service_instance = new ServiceInstance($request->all());
        $service_instance->save();
        return $service_instance;
    }

    public function delete($service_instance_id)
    {
        if ( ServiceInstance::where('id',$service_instance_id)->delete() ) {
            return [true];
        }
    }

}
