<?php

namespace App\Libraries;

use \App\Module;
use \App\ModuleInstance;
use \App\ModuleVersion;
use \App\APIUser;
use \App\Resource;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use Illuminate\Http\Request;

class ExecService {

    public function build_routes($service_instance,$service_version) {
        foreach($service_version->routes as $route_index =>$route) {
            $extra = [];
            if (isset($route->verb)) {$verb = $route->verb;} else { $verb = 'all';}
            if (isset($route->params)) {
                foreach($route->params as $param) {
                    if ($param->required === true || $param->required == 'true') {
                        $extra['required'][]=$param->name;
                    } else if ($param->required === false || $param->required == 'false' || $param->required=="0") {
                        $extra['optional'][]=$param->name;
                    }
                }
            }
            Router::add_route(
                '/'.$service_instance->slug.$route->path, 
                $service_instance->service->name, 
                $route->function_name, 
                $extra,
                $verb
            );
        }
    } 

    public function build_resources($service_instance) {
        /* Fetch and Configure Database for this Service Instance */
        $resources_arr = [];
        $resources_name_map = [];
        if (is_array($service_instance->resources)) {
            foreach($service_instance->resources as $resource_index => $resource_map) {
                $resources_arr[] = $resource_map->resource;
                $resources_name_map[$resource_map->resource] = $resource_map->name;
            }
        }

        $resources = Resource::whereIn('id',$resources_arr)->get();
        foreach($resources as $resource) {
            if ($resource->resource_type == 'mysql') {
                MySQLDB::config_database($resources_name_map[$resource->id],$resource->config);
            } else if ($resource->resource_type == 'oracle') {
                OracleDB::config_database($resources_name_map[$resource->id],$resource->config);
            } else if ($resource->resource_type == 'constant') {
                define($resources_name_map[$resource->id],$resource->config->value);
            }
        }

        /* Configure Lumen PDO Database Stuff -- Experimental*/
        foreach($resources as $resource) {
            if ($resource->resource_type == 'mysql') {
                config(['database.connections.'.$resources_name_map[$resource->id] =>[
                    'driver'    => 'mysql',
                    'port'      => isset($resource->config->port)?$resource->config->port:3306,
                    'host'      => isset($resource->config->server)?$resource->config->server:'',
                    'database'  => isset($resource->config->name)?$resource->config->name:'',
                    'username'  => isset($resource->config->user)?$resource->config->user:'',
                    'password'  => isset($resource->config->pass)?$resource->config->pass:'',
                ]]);
            } else if ($resource->resource_type == 'oracle') {
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
    }

    public function eval_code($service_instance, $service_version) {
        /* Evaluate Functions */
        $main_class = "\n".
            'use \App\Libraries\MySQLDB;'."\n".
            'use \App\Libraries\OracleDB;'."\n".
            'use Illuminate\Support\Facades\DB;'."\n\n".
            'class '.$service_instance->service->name.' {'."\n";

        foreach($service_version->functions as $function) {
            if ($function->name === 'Constructor') {
                $main_class .= 'function __construct($args=[]) {'."\n".
                    $function->content.
                    '}'."\n\n";
                } else {
                $main_class .= 'public function '.$function->name.'($args) {'."\n".
                    $function->content."\n".
                    '}'."\n\n";
                }
        }
        $main_class .= "}?>";
        // dd($main_class);
        eval($main_class);

        /* Evaluate Files */
        foreach($service_version->files as $code_file) {
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