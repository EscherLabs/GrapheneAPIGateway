<?php

namespace App\Http\Controllers;

use \App\Service;
use \App\ServiceInstance;
use \App\ServiceVersion;
use \App\APIUser;
use \App\Resource;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use \App\Libraries\ExecService;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function __construct() {
    }
    
    public function docs($slug) {
        $service_instance = ServiceInstance::where('slug',$slug)->with('service')->with('environment')->whereHas('environment', function($q){
            $q->where('domain','=',$_SERVER['HTTP_HOST']);
        })->first();
        if (is_null($service_instance)) {
            return response(json_encode(['error'=>'API Not Found']),404)->header('Content-type', 'application/json');;
        }
        $service_version = $service_instance->find_version();

        $user_id_arr = [];
        foreach($service_instance->route_user_map as $route_user_map_index => $route_user) {
            $user_id_arr[] = $route_user->api_user;
            $user_to_routes[$route_user->api_user][] = '/'.$service_instance->slug.$route_user->route;
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->get();
        $users = [];
        foreach($relevant_users as $user) {
            $users[$user->app_name] = ['user'=>$user, 'pass'=>$user->app_secret,'ips'=>[''],'routes'=>$user_to_routes[$user->id]];
        }
        $userisok = (isset($_SERVER['PHP_AUTH_USER']) && array_key_exists($_SERVER['PHP_AUTH_USER'],$users) && $users[$_SERVER['PHP_AUTH_USER']]['user']->check_app_secret($_SERVER['PHP_AUTH_PW']) );
        if (!($userisok)) {
            return response(json_encode(['error'=>'Unauthorized User']),401)
                ->header('WWW-Authenticate', 'Basic realm="'.$service_instance->name.' API"')
                ->header('Content-type', 'application/json');
        }

        $resources = Resource::all();
        return view('documentation', [
            'service_instance' => $service_instance, 
            'service_version'=>$service_version, 
            'users'=>$relevant_users,
            'resources'=>$resources,
        ]);
    }

    public function fetch($service_instance_id) {
        $service_instance = ServiceInstance::where('id',$service_instance_id)->with('service')->with('environment')->first();

        if (is_null($service_instance)) {
            return response(json_encode(['error'=>'API Not Found']),404)->header('Content-type', 'application/json');;
        }
        $service_version = $service_instance->find_version();
        $user_id_arr = [];
        foreach($service_instance->route_user_map as $route_user_map_index => $route_user) {
            $user_id_arr[] = $route_user->api_user;
            $user_to_routes[$route_user->api_user][] = '/'.$service_instance->slug.$route_user->route;
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->get();
        $resources = Resource::all();
        $data = [
            'service_instance' => $service_instance, 
            'service_version'=>$service_version, 
            'users'=>$relevant_users,
            'resources'=>$resources,
        ];
        $docs = view('documentation',$data)->render();
        $data['docs'] = $docs;
        return $data;
    }

}
