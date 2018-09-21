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
use \App\Libraries\ExecService;
use Illuminate\Http\Request;

class ExecController extends Controller
{
    public function __construct() {
    }
    
    public function exec($slug) {
        $exec_service = new ExecService();

        $service_instance = ServiceInstance::where('slug',$slug)->with('service')->first();
        if (is_null($service_instance)) {
            abort(404);
        }
        $service_version = $service_instance->find_version();
        $exec_service->build_routes($service_instance,$service_version);
        $users_arr = $exec_service->build_permissions($service_instance);
        ValidateUser::assert_valid_user($users_arr); // Bail if user is invalid!
        $exec_service->build_resources($service_instance);
        return $exec_service->eval_code($service_version);

    }   
}
