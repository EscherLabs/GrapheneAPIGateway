<?php

namespace App\Http\Controllers;

use \App\Module;
use \App\ModuleInstance;
use \App\ModuleVersion;
use \App\APIUser;
use \App\Libraries\Router;
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
        foreach($module_instance->route_user_map as $route_user_map_index => $route_user) {
            $user_id_arr[] = $route_user->api_user;
            $user_to_routes[$route_user->api_user][] = '/'.$slug.$route_user->route;
        }
        $relevant_users = APIUser::whereIn('id',$user_id_arr)->get();
        $users_arr = [];
        foreach($relevant_users as $user) {
            $users_arr[$user->app_name] = ['user'=>$user, 'pass'=>$user->app_secret,'ips'=>[''],'routes'=>$user_to_routes[$user->id]];
        }

        ValidateUser::assert_valid_user($users_arr);
        foreach($module_version->code as $code_file) {
            eval($code_file->content);
        }
        Router::handle_route();
    }   
}
