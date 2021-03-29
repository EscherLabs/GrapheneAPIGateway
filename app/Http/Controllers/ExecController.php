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

        $userisok = false; $ipisok = false; $routeisok = false;

        // Check Auth Credentials (or assume public if none are passed)
        $basic_auth_username = isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:'public';
        $basic_auth_password = isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:'';

        /* Validate Username & Password */
        $userisok = (isset($basic_auth_username) && array_key_exists($basic_auth_username,$users) && $users[$basic_auth_username]['user']->check_app_secret($basic_auth_password) );
        /* Validate IP Address */
        if ($userisok) {
            foreach($users[$basic_auth_username]['ips'] as $ip) {
                if (substr($_SERVER['REMOTE_ADDR'],0,strlen($ip)) == $ip) {
                    $ipisok = true;
                    break;
                }
            }
        }
        /* Validate Route */
        if ($ipisok == true && isset($users[$basic_auth_username]['routes'])) {
            foreach($users[$basic_auth_username]['routes'] as $path) {
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

    public function exec($slug,Request $request) {
        $exec_api = new ExecAPI();
        $api_instance = APIInstance::where('slug',$slug)->with('api')->whereHas('environment', function($q){
            $q->where('domain','=',app('request')->getHost());
        })->first();
        if (is_null($api_instance)) {
            return response(json_encode(['error'=>'API Not Found']),404)->header('Content-type', 'application/json');
        }
        $ret = $this->validate_user($api_instance);
        if ($ret !== true) {
            return $ret;
        }

        if ($api_instance->errors === 'all') {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else if ($api_instance->errors === 'none') {
            error_reporting(0);
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            config(['app.debug'=>false]);
            putenv("APP_DEBUG=false");
        }

        // Throw warnings as exceptions!
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });
        
        try {
            return $exec_api->eval_code($api_instance);
        } catch (\Exception $e) {
            if (ini_get('display_errors') == 1) {
                $error_line = $e->getLine();
                $file_contents = explode("\n",file_get_contents($e->getFile()));
                $file_contents_abridged = [];
                for ($line_number = $error_line-10; $line_number <= $error_line+10; $line_number++) {
                    if (isset($file_contents[$line_number])) {
                        $file_contents_abridged[] = str_pad($line_number+1,strlen(strval(count($file_contents))),' ',STR_PAD_LEFT).':  '.$file_contents[$line_number];
                    }
                }
                return response()->json([
                    'error' => [
                        'code' => 'error',
                        'message' => $e->getMessage(),
                        'line' => $error_line,
                        'file' => $e->getFile(),
                        'file_contents' => $file_contents_abridged,
                    ]
                ], 500);
            } else {
                return response()->json([
                    'error' => [
                        'code' => 'error',
                        'message' => $e->getMessage()
                    ]
                ], 500);
            }
        }
    }   
}
