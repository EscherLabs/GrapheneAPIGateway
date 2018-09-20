<?php

namespace App\Libraries;

use \App\Module;
use \App\ModuleInstance;
use \App\ModuleVersion;
use \App\APIUser;
use \App\DatabaseInstance;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use Illuminate\Http\Request;

class ExecService {

    public function build_routes($module_instance,$module_version,$slug) {
        foreach($module_version->routes as $route_index =>$route) {
            $extra = [];
            if (isset($route->verb)) {$extra['verb']=$route->verb;}
            if (isset($route->params)) {
                foreach($route->params as $param) {
                    if ($param->required === true || $param->required == 'true') {
                        $extra['required'][]=$param->name;
                    } else if ($param->required === false || $param->required == 'false') {
                        $extra['optional'][]=$param->name;
                    }
                }
            }
            $description = isset($route->description)?$route->description:'';

            Router::add_route(
                '/'.$slug.$route->path, 
                $module_instance->module->name, 
                $route->function_name, 
                $description, 
                $extra
            );
        }
    } 

    public function build_permissions($module_instance,$slug) {
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
        return $users_arr;
    }

    public function build_resources($module_instance) {
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
                config(['database.connections.'.$database_instance->database->name =>[
                    'driver'    => 'mysql',
                    'port'      => isset($database_instance->config->port)?$database_instance->config->port:3306,
                    'host'      => isset($database_instance->config->server)?$database_instance->config->server:'',
                    'database'  => isset($database_instance->config->name)?$database_instance->config->name:'',
                    'username'  => isset($database_instance->config->user)?$database_instance->config->user:'',
                    'password'  => isset($database_instance->config->pass)?$database_instance->config->pass:'',
                ]]);
            } else if ($database_instance->database->type == 'oracle') {
                config(['database.connections.'.$database_instance->database->name => [
                    'driver'        => 'oracle',
                    'tns'           => isset($database_instance->config->tns)?$database_instance->config->tns:'',
                    'port'          => isset($database_instance->config->port)?$database_instance->config->port:1521,
                    'username'      => isset($database_instance->config->user)?$database_instance->config->user:'',
                    'password'      => isset($database_instance->config->pass)?$database_instance->config->pass:'',
                    'charset'       => isset($database_instance->config->charset)?$database_instance->config->charset:'AL32UTF8',
                    'host'          => isset($database_instance->config->server)?$database_instance->config->server:'',
                    'database'      => isset($database_instance->config->name)?$database_instance->config->name:'',
                ]]);
            }
        }
    }

    public function eval_code($module_version) {
        /* Evaluate Code */
        foreach($module_version->code as $code_file) {
            $prepended_code = 
                ''."\n".
                'use \App\Libraries\MySQLDB;'."\n".
                'use \App\Libraries\OracleDB;'."\n".
                'use Illuminate\Support\Facades\DB;'."\n".
                ''."\n";
            eval($prepended_code.$code_file->content);
        }

        /* Run Code */
        $ret = Router::handle_route();
        if (!is_bool($ret) && !is_null($ret)) {
            return $ret;
        }
    }
}