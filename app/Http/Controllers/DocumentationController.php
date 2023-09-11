<?php

namespace App\Http\Controllers;

use \App\Models\API;
use \App\Models\APIInstance;
use \App\Models\APIVersion;
use \App\Models\APIUser;
use \App\Models\Resource;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function __construct() {
    }
    
    public function docs($slug) {
        $api_instance = APIInstance::where('slug',$slug)->with('api')->with('environment')->whereHas('environment', function($q){
            $q->where('domain','=',app('request')->getHost());
        })->first();
        if (is_null($api_instance)) {
            return response(json_encode(['error'=>'API Not Found']),404)->header('Content-type', 'application/json');;
        }
        $api_version = $api_instance->find_version();

        $user_id_arr = [];
        if (is_array($api_instance->route_user_map)) {
            foreach($api_instance->route_user_map as $route_user_map_index => $route_user) {
                $user_id_arr[] = $route_user->api_user;
                $user_to_routes[$route_user->api_user][] = '/'.$api_instance->slug.$route_user->route;
            }
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->whereHas('environment', function($q){
            $q->where('domain','=',app('request')->getHost());
        })->get();
        $users = [];
        foreach($relevant_users as $user) {
            $users[$user->app_name] = ['user'=>$user, 'pass'=>$user->app_secret,'ips'=>[''],'routes'=>$user_to_routes[$user->id]];
        }
        // Add 'public' user to users array
        if (isset($user_to_routes[0])) {
            $public_user = new APIUser(['app_name'=>'public','app_secret'=>'']);
            $users['public'] = ['user'=>$public_user,'pass'=>$public_user->app_secret,'ips'=>[''],'routes'=>$user_to_routes[0]];
        }
        
        // Check Auth Credentials (or assume public if none are passed)
        $basic_auth_username = isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:'public';
        $basic_auth_password = isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:'';
        
        $userisok = (isset($basic_auth_username) && array_key_exists($basic_auth_username,$users) && $users[$basic_auth_username]['user']->check_app_secret($basic_auth_password) );
        if (!($userisok)) {
            return response(json_encode(['error'=>'Unauthorized User']),401)
                ->header('WWW-Authenticate', 'Basic realm="'.$api_instance->name.' API"')
                ->header('Content-type', 'application/json');
        }

        $resources = Resource::all();
        return view('documentation', [
            'api_instance' => $api_instance, 
            'api_version'=>$api_version, 
            'users'=>$relevant_users,
            'resources'=>$resources,
        ]);
    }

    public function fetch($api_instance_id) {
        $api_instance = APIInstance::where('id',$api_instance_id)->with('api')->with('environment')->first();

        if (is_null($api_instance)) {
            return response(json_encode(['error'=>'API Not Found']),404)->header('Content-type', 'application/json');;
        }
        $api_version = $api_instance->find_version();
        $user_id_arr = [];
        if (is_array($api_instance->route_user_map)) {
            foreach($api_instance->route_user_map as $route_user_map_index => $route_user) {
                $user_id_arr[] = $route_user->api_user;
                $user_to_routes[$route_user->api_user][] = '/'.$api_instance->slug.$route_user->route;
            }
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->get();
        $resources = Resource::all();
        $data = [
            'api_instance' => $api_instance, 
            'api_version'=>$api_version, 
            'users'=>$relevant_users,
            'resources'=>$resources,
        ];
        $docs = view('documentation',$data)->render();
        $data['docs'] = $docs;
        return $data;
    }

}
