<?php

namespace App\Http\Controllers;

use \App\Service;
use \App\ServiceVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use \Carbon\Carbon;

class ServicesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Service::all();
    }   

    public function read($service_id)
    {
        $service = Service::where('id',$service_id)->first();
        if (!is_null($service)) {
            return $service;
        } else {
            return response('service not found', 404);
        }
    }

    public function versions($service_id)
    {
        $versions = ServiceVersion::select('id','summary','description','stable','user_id','created_at','updated_at')
            ->orderby('updated_at')
            ->where('service_id',$service_id)->get();
        if (!is_null($versions)) {
            return $versions;
        } else {
            return response('service not found', 404);
        }
    }

    public function latest_version($service_id)
    {
        $service_version = ServiceVersion::where('service_id','=',$service_id)->orderBy('created_at', 'desc')->first();
        if (!is_null($service_version)) {
            return $service_version;
        } else {
            return response('service has no latest version', 404);
        }
    }


    public function edit(Request $request, $service_id)
    {
        $service = Service::where('id',$service_id)->first();
        if (!is_null($service)) {
            $service->update($request->all());
            return $service;
        } else {
            return response('service not found', 404);
        }
    }

    public function add(Request $request)
    {
        $service = new Service($request->all());
        $service->save();

        $service_version = new ServiceVersion($request->all());
        $service_version->service_id = $service->id;
        $service_version->save();
        return $service;
    }

    public function delete($service_id)
    {
        if ( Service::where('id',$service_id)->delete() ) {
            return [true];
        }
    }

    public function publish(Request $request, $service_id) {
        $service_version = ServiceVersion::where('service_id','=',$service_id)->orderBy('created_at', 'desc')->where('stable','=',0)->first();
        if($service_version) {
            $service_version->summary = $request->summary;
            $service_version->description = $request->description;
            $service_version->stable = true;
            if ($request->has('user_id')) {
                $service_version->user_id = $request->user_id;
            }
            $service_version->save();
            return $service_version;
        }else{
            abort(409, 'No changes have been made since last published.<br><br> To publish please save changes.');
        }
    }

    public function code(Request $request, $service_id) { 
        $service_version = ServiceVersion::where('service_id','=',$service_id)->orderBy('created_at', 'desc')->first();
        $post_data = Input::all();
        if(!isset($post_data['updated_at']) && !isset($post_data['force']) ){
            abort(403, $service_version);
        }

        $first = Carbon::parse($post_data['updated_at']);
        $second = Carbon::parse($service_version->updated_at);

        if(is_null($service_version) || $service_version->stable){
            $service_version = new ServiceVersion();
            $service_version->service_id = $service_id;
        }else if(!($first->gte($second) || isset($post_data['force']))){
            abort(409, $service_version);
        }
        $service_version->files = $request->input('files');
        $service_version->functions = $request->functions;
        $service_version->resources = $request->resources;
        if ($request->has('routes')) {
            $service_version->routes = $request->routes;
        }
        $service_version->user_id = null;
        $service_version->save();
        return $service_version;
    }



}
