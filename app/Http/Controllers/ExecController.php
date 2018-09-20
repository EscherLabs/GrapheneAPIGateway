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
use \App\Libraries\ExecService;
use Illuminate\Http\Request;

class ExecController extends Controller
{
    public function __construct() {
    }
    
    public function exec($slug) {
        $exec_service = new ExecService();

        $module_instance = ModuleInstance::where('slug',$slug)->with('module')->first();
        if (is_null($module_instance)) {
            abort(404);
        }
        $module_version = ModuleVersion::where('id',$module_instance->module_version_id)->first();
        $exec_service->build_routes($module_instance,$module_version,$slug);
        $users_arr = $exec_service->build_permissions($module_instance,$slug);
        ValidateUser::assert_valid_user($users_arr); // Bail if user is invalid!
        $exec_service->build_resources($module_instance);
        return $exec_service->eval_code($module_version);

    }   
}
