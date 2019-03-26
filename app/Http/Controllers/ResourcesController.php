<?php

namespace App\Http\Controllers;

use \App\Resource;
use Illuminate\Http\Request;

class ResourcesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Resource::all();
    }   

    public function browse_by_type($type) {
        $resources  = Resource::where('type',$type)->get();
        return $resources;
    }  

    public function read($resource_id) {
        $resource  = Resource::where('id',$resource_id)->first();
        if (!is_null($resource)) {
            return $resource;
        } else {
            return response('resource not found', 404);
        }
    }  

    public function edit(Request $request, $resource_id)
    {
        $resource = Resource::where('id',$resource_id)->first();
        if (!is_null($resource)) {
            $resource->update($request->all());
            return $resource;
        } else {
            return response('resource not found', 404);
        }
    }

    public function add(Request $request)
    {
        $resource = new Resource($request->all());
        $resource->save();
        return $resource;
    }

    public function delete($resource_id)
    {
        if ( Resource::where('id',$resource_id)->delete() ) {
            return [true];
        }
    }
}
