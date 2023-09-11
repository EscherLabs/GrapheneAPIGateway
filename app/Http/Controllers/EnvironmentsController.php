<?php

namespace App\Http\Controllers;

use \App\Models\Environment;
use \App\Models\APIUser;
use Illuminate\Http\Request;

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
            return response('environment not found', 404);
        }
    }

    public function edit(Request $request, $environment_id)
    {
        $environment = Environment::where('id',$environment_id)->first();
        if (!is_null($environment)) {
            $environment->update($request->all());
            return $environment;
        } else {
            return response('environment not found', 404);
        }
    }

    public function add(Request $request)
    {
        $environment = new Environment($request->all());
        $environment->save();
        $apiuser = new APIUser(["app_name"=>"public","app_secret"=>"public","environment_id"=>$environment->id]);
        $apiuser->save();
        return $environment;
    }

    public function delete($environment_id)
    {
        if ( Environment::where('id',$environment_id)->delete() ) {
            return [true];
        }
    }

    public function api_users($environment_id)
    {
        $api_users = APIUser::where('environment_id',$environment_id)->get();
        return $api_users;
    }

}
