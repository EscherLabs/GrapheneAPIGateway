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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PDO;

class ExecAPI {

    private function build_routes($api_instance,$api_version) {
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

    private function build_resources($api_instance) {
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
            } else if ($resource->resource_type == 'sqlsrv') {
                config(['app.apiresources.'.$resources_name_map[$resource->id]=>$resource->config_with_secrets]);
                config(['database.connections.'.$resources_name_map[$resource->id] => [
                    'driver'        => 'sqlsrv',
                    'host'          => isset($resource->config->server)?$resource->config->server:'',
                    'database'      => isset($resource->config->name)?$resource->config->name:'',
                    'username'      => isset($resource->config->user)?$resource->config->user:'',
                    'password'      => isset($resource->config_with_secrets->pass)?$resource->config_with_secrets->pass:'',
                    'port'          => isset($resource->config->port)?$resource->config->port:1433,
                    'options'       => [ PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT ],
                ]]);
            } else if ($resource->resource_type == 'secret' || $resource->resource_type == 'value') {
                config(['app.apiresources.'.$resources_name_map[$resource->id]=>$resource->config_with_secrets->value]);
            }
        }
    }

    private function build_file($filename=null, $api_instance, $api_version) {
        $file_content = '';
        if (is_null($filename)) {
            $file_content = '<?php'."\n".
                'use \App\Libraries\MySQLDB;'."\n".
                'use \App\Libraries\OracleDB;'."\n".
                'use Illuminate\Support\Facades\DB;'."\n".
                'use Illuminate\Support\Arr;'."\n".
                'use Illuminate\Support\Facades\Mail;'."\n".
                'use \Carbon\Carbon;'."\n\n".
                'class '.$api_instance->api->name.' {'."\n";

            foreach($api_version->functions as $function) {
                if ($function->name === 'Constructor') {
                    $file_content .= 'function __construct($args=[]) {'."\n".
                        $function->content.
                        '}'."\n\n";
                    } else {
                    $file_content .= 'public function '.$function->name.'($args=[],$resources=[]) {'."\n".
                        $function->content."\n".
                        '}'."\n\n";
                    }
            }
            $file_content .= "}";
        } else {
            foreach($api_version->files as $code_file) {
                if ($code_file->name === $filename) {
                    $prepended_code = 
                        '<?php'."\n".
                        'use \App\Libraries\MySQLDB;'."\n".
                        'use \App\Libraries\OracleDB;'."\n".
                        'use Illuminate\Support\Facades\DB;'."\n".
                        'use Illuminate\Support\Arr;'."\n".
                        'use Illuminate\Support\Facades\Mail;'."\n".
                        'use \Carbon\Carbon;'."\n".  
                        '?>'."\n";
                    $file_content = $prepended_code.$code_file->content;
                    break;
                }
            }
        }
        return $file_content;
    }

    private function build_module_cache($code_path, $api_instance) {
        $api_version_id = $api_instance->find_version_id();
        $api_version_metadata = APIVersion::select('id','updated_at')->where('id',$api_version_id)->first();

        if (file_exists($code_path.DIRECTORY_SEPARATOR.'api_version.json')) {
            $api_version = json_decode(file_get_contents($code_path.DIRECTORY_SEPARATOR.'api_version.json'));
            if ($api_version_id == $api_version_metadata->id && $api_version->updated_at == $api_version_metadata->updated_at->toJSON()) {
                // Local Cache looks 'ok' and is up-to-date.  No need to update cache.
                return $api_version;
            }
        }

        if (!file_exists($code_path)) { mkdir($code_path, 0777, true); }
        $api_version = APIVersion::where('id',$api_version_id)->first();
        
        $main_class = $this->build_file(null,$api_instance, $api_version);
        file_put_contents($code_path.DIRECTORY_SEPARATOR.'Module.php',$main_class);
        
        foreach($api_version->files as $code_file) {
            $file_code = $this->build_file($code_file->name,$api_instance, $api_version);
            file_put_contents($code_path.DIRECTORY_SEPARATOR.$code_file->name,$file_code);
        }

        // Clear out File Content in App Version Cache to reduce size (no need for that)
        $api_version->files = collect($api_version->files)->map(function($item,$key) {
            return isset($item->name)?(Object)['name'=>$item->name]:(Object)[];
        });
        // Store Local copy of App Version, excluding File Content to reduce size (no need for that) 
        file_put_contents($code_path.DIRECTORY_SEPARATOR.'api_version.json',
            json_encode(Arr::except($api_version->toArray(),['functions']))
        );  
        return $api_version;
    }

    public function eval_code($api_instance) {
        $code_path = app()->basePath().DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'modules_cache'.DIRECTORY_SEPARATOR.$api_instance->id;
        $api_version = $this->build_module_cache($code_path, $api_instance);

        $this->build_routes($api_instance,$api_version);
        $this->build_resources($api_instance);

        /* Dynamically Include Relevant Files */
        include_once($code_path.DIRECTORY_SEPARATOR.'Module.php');
        foreach($api_version->files as $code_file) {
            include_once($code_path.DIRECTORY_SEPARATOR.$code_file->name);
        }

        // Disconnect from the APIGateay database while we execute the code.
        // This is to avoid having too many simulataenous connections to the database,
        // especially when a given API takes a very long time to execute.
        DB::connection()->disconnect(); 

        /* Run Code */
        $ret = Router::handle_route();
        if (!is_bool($ret) && !is_null($ret)) {
            return $ret;
        }
    }
}