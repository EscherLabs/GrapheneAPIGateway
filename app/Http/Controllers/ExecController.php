<?php

namespace App\Http\Controllers;

use \App\API;
use \App\APIInstance;
use \App\APIVersion;
use \App\APIUser;
use \App\Resource;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use \App\Libraries\ExecAPI;
use Illuminate\Http\Request;

class ExecController extends Controller
{
    public function __construct() {
    }

    private function validate_user($api_instance) {
        $user_id_arr = [];
        foreach($api_instance->route_user_map as $route_user_map_index => $route_user) {
            $user_id_arr[] = $route_user->api_user;
            $user_to_routes[$route_user->api_user][] = '/'.$api_instance->slug.$route_user->route;
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->whereHas('environment', function($q){
            $q->where('domain','=',$_SERVER['HTTP_HOST']);
        })->get();
        
        $users = [];
        foreach($relevant_users as $user) {
            $users[$user->app_name] = ['user'=>$user, 'pass'=>$user->app_secret,'ips'=>[''],'routes'=>$user_to_routes[$user->id]];
        }
        $userisok = false; $ipisok = false; $routeisok = false;
        /* Validate Username & Password */
        $userisok = (isset($_SERVER['PHP_AUTH_USER']) && array_key_exists($_SERVER['PHP_AUTH_USER'],$users) && $users[$_SERVER['PHP_AUTH_USER']]['user']->check_app_secret($_SERVER['PHP_AUTH_PW']) );
        /* Validate IP Address */
        if ($userisok) {
            foreach($users[$_SERVER['PHP_AUTH_USER']]['ips'] as $ip) {
                if (substr($_SERVER['REMOTE_ADDR'],0,strlen($ip)) == $ip) {
                    $ipisok = true;
                    break;
                }
            }
        }
        /* Validate Route */
        if ($ipisok == true && isset($users[$_SERVER['PHP_AUTH_USER']]['routes'])) {
            foreach($users[$_SERVER['PHP_AUTH_USER']]['routes'] as $path) {
                $path = '/^'.str_replace('/','\/',$path).'/';
                if (preg_match($path, $_SERVER['REQUEST_URI']) == 1) {
                    $routeisok = true;
                    break;
                }
            }
        }
        if (!($userisok && $ipisok && $routeisok)) {
            return response(json_encode(['error'=>'Unauthorized User']),401)
                ->header('WWW-Authenticate', 'Basic realm="'.$api_instance->name.' API"')
                ->header('Content-type', 'application/json');
        } else {
            return true;
        }
    }

    public function exec($slug) {
        $exec_api = new ExecAPI();
        $api_instance = APIInstance::where('slug',$slug)->with('api')->whereHas('environment', function($q){
            $q->where('domain','=',$_SERVER['HTTP_HOST']);
        })->first();
        if (is_null($api_instance)) {
            return response(json_encode(['error'=>'API Not Found']),404)->header('Content-type', 'application/json');
        }
        $api_version = $api_instance->find_version();
        $ret = $this->validate_user($api_instance);
        if ($ret !== true) {
            return $ret;
        }

        $exec_api->build_routes($api_instance,$api_version);
        $exec_api->build_resources($api_instance);
        return $exec_api->eval_code($api_instance,$api_version);

    }   
}
