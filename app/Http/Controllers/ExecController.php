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
use Illuminate\Http\Request;

class ExecController extends Controller
{
    public function __construct() {
    }
    
    public function exec($slug) {

        $service_instance = ServiceInstance::where('slug',$slug)->with('service')->first();
        if (is_null($service_instance)) {
            abort(404);
        }
        $service_version = ServiceVersion::where('id',$service_instance->service_version_id)->first();
        foreach($service_version->routes as $route_index =>$route) {
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
                $service_instance->service->name, 
                $route->function_name, 
                $description, 
                $extra
            );
        }

        /* Fetch and Enforce Permissions for this Service Instance & Route */
        $user_id_arr = [];
        foreach($service_instance->route_user_map as $route_user_map_index => $route_user) {
            $user_id_arr[] = $route_user->api_user;
            $user_to_routes[$route_user->api_user][] = '/'.$slug.$route_user->route;
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->get();
        $users_arr = [];
        foreach($relevant_users as $user) {
            $users_arr[$user->app_name] = ['user'=>$user, 'pass'=>$user->app_secret,'ips'=>[''],'routes'=>$user_to_routes[$user->id]];
        }
        ValidateUser::assert_valid_user($users_arr); // Bail if user is invalid!

        /* Fetch and Configure Database for this Service Instance */
        $resources_arr = [];
        $resources_name_map = [];
        foreach($service_instance->resources as $resource_index => $resource_map) {
            $resources_arr[] = $resource_map->resource;
            $resources_name_map[$resource_map->resource] = $resource_map->name;
        }
        $resources = Resource::whereIn('id',$resources_arr)->get();
        foreach($resources as $resource) {
            if ($resource->type == 'mysql') {
                MySQLDB::config_database($resources_name_map[$resource->id],$resource->config);
            } else if ($resource->type == 'oracle') {
                OracleDB::config_database($resources_name_map[$resource->id],$resource->config);
            } else if ($resource->type == 'constant') {
                define($resources_name_map[$resource->id],$resource->config->value);
            }
        }

        /* Configure Lumen PDO Database Stuff -- Experimental*/
        foreach($resources as $resource) {
            if ($resource->type == 'mysql') {
                config(['database.connections.'.$resources_name_map[$resource->id] =>[
                    'driver'    => 'mysql',
                    'port'      => isset($resource->config->port)?$resource->config->port:3306,
                    'host'      => isset($resource->config->server)?$resource->config->server:'',
                    'database'  => isset($resource->config->name)?$resource->config->name:'',
                    'username'  => isset($resource->config->user)?$resource->config->user:'',
                    'password'  => isset($resource->config->pass)?$resource->config->pass:'',
                ]]);
            } else if ($resource->type == 'oracle') {
                config(['database.connections.'.$resources_name_map[$resource->id] => [
                    'driver'        => 'oracle',
                    'tns'           => isset($resource->config->tns)?$resource->config->tns:'',
                    'port'          => isset($resource->config->port)?$resource->config->port:1521,
                    'username'      => isset($resource->config->user)?$resource->config->user:'',
                    'password'      => isset($resource->config->pass)?$resource->config->pass:'',
                    'charset'       => isset($resource->config->charset)?$resource->config->charset:'AL32UTF8',
                    'host'          => isset($resource->config->server)?$resource->config->server:'',
                    'database'      => isset($resource->config->name)?$resource->config->name:'',
                ]]);
            }
        }

        /* Evaluate Code */
        foreach($service_version->code as $code_file) {
            $prepended_code = 
                '?><?php'."\n".
                'use \App\Libraries\MySQLDB;'."\n".
                'use \App\Libraries\OracleDB;'."\n".
                'use Illuminate\Support\Facades\DB;'."\n".
                '?>'."\n";
            eval($prepended_code.$code_file->content);
        }

        /* Run Code */
        $ret = Router::handle_route();
        if (!is_bool($ret) && !is_null($ret)) {
            return $ret;
        }
    }   
}
