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

class DocumentationController extends Controller
{
    public function __construct() {
    }
    
    public function docs($slug) {
        $service_instance = ServiceInstance::where('slug',$slug)->with('service')->first();
        if (is_null($service_instance)) {
            abort(404);
        }
        $service_version = $service_instance->find_version();
// dd($service_instance);
        return view('documentation', ['service_instance' => $service_instance, 'service_version'=>$service_version]);
    }
}
