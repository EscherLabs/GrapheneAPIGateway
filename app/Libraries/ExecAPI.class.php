<?php

namespace App\Libraries;

use \App\API;
use \App\APIInstance;
use \App\APIVersion;
use \App\APIUser;
use \App\Resource;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use Illuminate\Http\Request;

class ExecAPI {

    public function build_routes($api_instance,$api_version) {
        foreach($api_version->routes as $route_index =>$route) {
            $extra = [];
            if (isset($route->verb)) {$verb = $route->verb;} else { $verb = 'all';}
            if (isset($route->required)) {
                foreach($route->required as $required_param) {
                    $extra['required'][]=$required_param->name;
                }
            }
            Router::add_route(
                '/'.$api_instance->slug.$route->path, 
                $api_instance->api->name, 
                $route->function_name, 
                $extra,
                $verb
            );
        }
    } 

    public function build_resources($api_instance) {
        /* Fetch and Configure Database for this API Instance */
        $resources_arr = [];
        $resources_name_map = [];
        if (is_array($api_instance->resources)) {
            foreach($api_instance->resources as $resource_index => $resource_map) {
                $resources_arr[] = $resource_map->resource;
                $resources_name_map[$resource_map->resource] = $resource_map->name;
            }
        }

        $resources = Resource::whereIn('id',$resources_arr)->get();
        foreach($resources as $resource) {
            if ($resource->resource_type == 'mysql') {
                MySQLDB::config_database($resources_name_map[$resource->id],$resource->config_with_secrets);
                config(['app.apiresources.'.$resources_name_map[$resource->id]=>$resource->config_with_secrets]);
                config(['database.connections.'.$resources_name_map[$resource->id] =>[
                    'driver'    => 'mysql',
                    'port'      => isset($resource->config->port)?$resource->config->port:3306,
                    'host'      => isset($resource->config->server)?$resource->config->server:'',
                    'database'  => isset($resource->config->name)?$resource->config->name:'',
                    'username'  => isset($resource->config->user)?$resource->config->user:'',
                    'password'  => isset($resource->config_with_secrets->pass)?$resource->config_with_secrets->pass:'',
                ]]);
            } else if ($resource->resource_type == 'oracle') {
                OracleDB::config_database($resources_name_map[$resource->id],$resource->config_with_secrets);
                config(['app.apiresources.'.$resources_name_map[$resource->id]=>$resource->config_with_secrets]);
                config(['database.connections.'.$resources_name_map[$resource->id] => [
                    'driver'        => 'oracle',
                    'tns'           => isset($resource->config->tns)?$resource->config->tns:'',
                    'port'          => isset($resource->config->port)?$resource->config->port:1521,
                    'username'      => isset($resource->config->user)?$resource->config->user:'',
                    'password'      => isset($resource->config_with_secrets->pass)?$resource->config_with_secrets->pass:'',
                    'charset'       => isset($resource->config->charset)?$resource->config->charset:'AL32UTF8',
                    'host'          => isset($resource->config->server)?$resource->config->server:'',
                    'database'      => isset($resource->config->name)?$resource->config->name:'',
                ]]);
            } else if ($resource->resource_type == 'secret' || $resource->resource_type == 'value') {
                config(['app.apiresources.'.$resources_name_map[$resource->id]=>$resource->config_with_secrets->value]);
            }
        }
    }

    public function eval_code($api_instance, $api_version) {
        /* Evaluate Functions */
        $main_class = "\n".
            'use \App\Libraries\MySQLDB;'."\n".
            'use \App\Libraries\OracleDB;'."\n".
            'use Illuminate\Support\Facades\DB;'."\n\n".
            'use Illuminate\Support\Arr;'."\n\n".
            'use \Carbon\Carbon;'."\n\n".
            'class '.$api_instance->api->name.' {'."\n";

        foreach($api_version->functions as $function) {
            if ($function->name === 'Constructor') {
                $main_class .= 'function __construct($args=[]) {'."\n".
                    $function->content.
                    '}'."\n\n";
                } else {
                $main_class .= 'public function '.$function->name.'($args=[],$resources=[]) {'."\n".
                    $function->content."\n".
                    '}'."\n\n";
                }
        }
        $main_class .= "}?>";
        // dd($main_class);
        eval($main_class);

        /* Evaluate Files */
        foreach($api_version->files as $code_file) {
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