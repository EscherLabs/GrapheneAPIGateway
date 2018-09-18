<?php

namespace App\Http\Controllers;

use \App\ServiceVersion;
use Illuminate\Http\Request;

class ServiceVersionsController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return ServiceVersion::all();
    }   

    public function read($service_version_id)
    {
        $service_version = ServiceVersion::where('id',$service_version_id)->first();
        if (!is_null($service_version)) {
            return $service_version;
        } else {
            return response('service_version not found', 404);
        }
    }

    public function edit(Request $request, $service_version_id)
    {
        $service_version = ServiceVersion::where('id',$service_version_id)->first();
        if (!is_null($service_version)) {
            $service_version->update($request->all());
            return $service_version;
        } else {
            return response('service_version not found', 404);
        }
    }

    public function add(Request $request)
    {
        $service_version = new ServiceVersion($request->all());
        $service_version->save();
        return $service_version;
    }

    public function delete($service_version_id)
    {
        if ( ServiceVersion::where('id',$service_version_id)->delete() ) {
            return [true];
        }
    }

}
