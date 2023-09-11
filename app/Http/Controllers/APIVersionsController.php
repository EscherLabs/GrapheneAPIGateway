<?php

namespace App\Http\Controllers;

use \App\Models\APIVersion;
use Illuminate\Http\Request;

class APIVersionsController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return APIVersion::select('api_id','description','id','stable','summary','user_id','created_at','updated_at')
            ->orderby('api_id')->orderby('summary')->get();
    }   

    public function read($api_version_id)
    {
        $api_version = APIVersion::where('id',$api_version_id)->first();
        if (!is_null($api_version)) {
            return $api_version;
        } else {
            return response('api_version not found', 404);
        }
    }

    public function edit(Request $request, $api_version_id)
    {
        $api_version = APIVersion::where('id',$api_version_id)->first();
        if (!is_null($api_version)) {
            $api_version->update($request->all());
            return $api_version;
        } else {
            return response('api_version not found', 404);
        }
    }

    public function add(Request $request)
    {
        $api_version = new APIVersion($request->all());
        $api_version->save();
        return $api_version;
    }

    public function delete($api_version_id)
    {
        if ( APIVersion::where('id',$api_version_id)->delete() ) {
            return [true];
        }
    }

}
