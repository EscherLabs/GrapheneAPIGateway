<?php

namespace App\Http\Controllers;

use \App\APIUser;

class APIUsersController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return APIUser::all();
    }   

    public function read($apiuser_id)
    {
        $apiuser = APIUser::where('id',$apiuser_id)->first();
        if (!is_null($apiuser)) {
            return $apiuser;
        } else {
            return response('apiuser not found', 404);
        }
    }

    public function edit(Request $request, $apiuser_id)
    {
        $apiuser = APIUser::where('id',$apiuser_id)->first();
        if (!is_null($apiuser)) {
            $apiuser->update($request->all());
            return $apiuser;
        } else {
            return response('apiuser not found', 404);
        }
    }

    public function add(Request $request)
    {
        $apiuser = new APIUser($request->all());
        $apiuser->save();
        return $apiuser;
    }

    public function delete($apiuser_id)
    {
        if ( APIUser::where('id',$apiuser_id)->delete() ) {
            return [true];
        }
    }
}
