<?php

namespace App\Http\Controllers;

use \App\API;
use \App\APIVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use \Carbon\Carbon;

class APIsController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return API::all();
    }   

    public function read($api_id)
    {
        $api = API::where('id',$api_id)->first();
        if (!is_null($api)) {
            return $api;
        } else {
            return response('api not found', 404);
        }
    }

    public function versions($api_id)
    {
        $versions = APIVersion::select('id','summary','description','created_at','user_id')
            ->where('api_id',$api_id)->where('stable','=',1)
            ->orderby('created_at','desc')->get();
        if (!is_null($versions)) {
            foreach($versions as $i => $version) {
                $versions[$i]->label = $version->created_at->format('Y-m-d').' - '.$version->summary;
            }    
            return $versions;
        } else {
            return response('api not found', 404);
        }
    }

    public function latest_version($api_id)
    {
        $api_version = APIVersion::where('api_id','=',$api_id)->orderBy('created_at', 'desc')->first();
        if (!is_null($api_version)) {
            return $api_version;
        } else {
            return response('api has no latest version', 404);
        }
    }


    public function edit(Request $request, $api_id)
    {
        $api = API::where('id',$api_id)->first();
        if (!is_null($api)) {
            $api->update($request->all());
            return $api;
        } else {
            return response('api not found', 404);
        }
    }

    public function add(Request $request)
    {
        $api = new API($request->all());
        $api->save();

        $api_version = new APIVersion($request->all());
        $api_version->api_id = $api->id;
        $api_version->save();
        return $api;
    }

    public function delete($api_id)
    {
        if ( API::where('id',$api_id)->delete() ) {
            return [true];
        }
    }

    public function publish(Request $request, $api_id) {
        $api_version = APIVersion::where('api_id','=',$api_id)->orderBy('created_at', 'desc')->where('stable','=',0)->first();
        if($api_version) {
            $api_version->summary = $request->summary;
            $api_version->description = $request->description;
            $api_version->stable = true;
            if ($request->has('user_id')) {
                $api_version->user_id = $request->user_id;
            }
            $api_version->save();
            return $api_version;
        }else{
            abort(409, 'No changes have been made since last published.<br><br> To publish please save changes.');
        }
    }

    public function code(Request $request, $api_id) { 
        $api_version = APIVersion::where('api_id','=',$api_id)->orderBy('created_at', 'desc')->first();
        $post_data = Input::all();
        if(!isset($post_data['updated_at']) && !isset($post_data['force']) ){
            abort(403, $api_version);
        }

        $first = Carbon::parse($post_data['updated_at']);
        $second = Carbon::parse($api_version->updated_at);

        if(is_null($api_version) || $api_version->stable){
            $api_version = new APIVersion();
            $api_version->api_id = $api_id;
        }else if(!($first->gte($second) || isset($post_data['force']))){
            abort(409, $api_version);
        }
        $api_version->files = $request->input('files');
        $api_version->functions = $request->functions;
        $api_version->resources = $request->resources;
        if ($request->has('routes')) {
            $api_version->routes = $request->routes;
        }
        $api_version->user_id = null;
        $api_version->save();
        return $api_version;
    }



}