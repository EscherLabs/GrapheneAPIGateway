<?php

namespace App\Http\Controllers;

use \App\Module;
use \App\ModuleInstance;
use \App\ModuleVersion;
use \App\APIUser;
use \App\DatabaseInstance;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;

class ExecController extends Controller
{
    public function __construct() {
    }
    
    public function exec($slug) {

        $module_instance = ModuleInstance::where('slug',$slug)->with('module')->first();
        if (is_null($module_instance)) {
            abort(404);
        }
        // return $module_instance;
        $module_version = ModuleVersion::where('id',$module_instance->module_version_id)->first();
        foreach($module_version->routes as $route_index =>$route) {
            $required_params = explode(',',$route->required);
            $optional_params = explode(',',$route->optional);
            $extra = [];
            if (isset($route->verb)) {$extra['verb']=$route->verb;}
            if (count($required_params)>0 && $required_params[0]!='') {$extra['required']=$required_params;}
            if (count($optional_params)>0 && $optional_params[0]!='') {$extra['optional']=$optional_params;}

            Router::add_route(
                '/'.$slug.$route->path, 
                $module_instance->module->name, 
                $route->function_name, 
                $route->description, 
                $extra
            );
        }

        /* Fetch and Enforce Permissions for this Module Instance & Route */
        $user_id_arr = [];
        foreach($module_instance->route_user_map as $route_user_map_index => $route_user) {
            $user_id_arr[] = $route_user->api_user;
            $user_to_routes[$route_user->api_user][] = '/'.$slug.$route_user->route;
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->get();
        $users_arr = [];
        foreach($relevant_users as $user) {
            $users_arr[$user->app_name] = ['user'=>$user, 'pass'=>$user->app_secret,'ips'=>[''],'routes'=>$user_to_routes[$user->id]];
        }
        ValidateUser::assert_valid_user($users_arr); // Bail if user is invalid!

        /* Fetch and Configure Database for this Module Instance */
        $database_instance_arr = [];
        foreach($module_instance->database_instance_map as $database_map_index => $database_map) {
            $database_instance_arr[] = $database_map->database_instance;
        }
        $database_instances = DatabaseInstance::whereIn('id',$database_instance_arr)->with('database')->get();
        $databases_config = [];
        foreach($database_instances as $database_instance) {
            if ($database_instance->database->type == 'mysql') {
                MySQLDB::config_database($database_instance->database->name,$database_instance->config);
            } else if ($database_instance->database->type == 'oracle') {
                OracleDB::config_database($database_instance->database->name,$database_instance->config);
            }
        }

        /* Configure Lumen PDO Database Stuff -- Experimental*/
        foreach($database_instances as $database_instance) {
            if ($database_instance->database->type == 'mysql') {
                MySQLDB::config_database($database_instance->database->name,$database_instance->config);
                config(['database.connections.'.$database_instance->database->name=>[
                    'driver'    => 'mysql',
                    'port'      => 3306,
                    'host'      => $database_instance->config->server,
                    'database'  => $database_instance->config->name,
                    'username'  => $database_instance->config->user,
                    'password'  => $database_instance->config->pass,
                ]]);
            }
        }

        /* Evaluate Code */
        foreach($module_version->code as $code_file) {
            $prepended_code = 
                'use \App\Libraries\MySQLDB;'."\n".
                'use \App\Libraries\OracleDB;'."\n".
                'use Illuminate\Support\Facades\DB;'."\n";
            eval($prepended_code.$code_file->content);
        }

        /* Run Code */
        return Router::handle_route();
    }   
}
