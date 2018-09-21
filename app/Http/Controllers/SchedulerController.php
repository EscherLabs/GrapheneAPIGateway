<?php

namespace App\Http\Controllers;

use Cron\CronExpression;
use \App\Service;
use \App\ServiceInstance;
use \App\ServiceVersion;
use \App\APIUser;
use \App\DatabaseInstance;
use \App\Scheduler;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use \App\Libraries\ExecService;
use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Scheduler::all();
    }   

    public function read($scheduler_id)
    {
        $scheduler = Scheduler::where('id',$scheduler_id)->first();
        if (!is_null($scheduler)) {
            return $scheduler;
        } else {
            return response('scheduler not found', 404);
        }
    }

    public function edit(Request $request, $scheduler_id)
    {
        $scheduler = Scheduler::where('id',$scheduler_id)->first();
        if (!is_null($scheduler)) {
            $scheduler->update($request->all());
            return $scheduler;
        } else {
            return response('scheduler not found', 404);
        }
    }

    public function add(Request $request)
    {
        $scheduler = new Scheduler($request->all());
        $scheduler->save();
        return $scheduler;
    }

    public function delete($scheduler_id)
    {
        if ( Scheduler::where('id',$scheduler_id)->delete() ) {
            return [true];
        }
    }

    public function run($scheduler_id)
    {
        $task = Scheduler::where('id',$scheduler_id)->first();
        if (!is_null($task)) {
            $exec_service = new ExecService();
            $service_instance = ServiceInstance::where('id',$task->service_instance_id)->with('Service')->first();
            if (is_null($service_instance)) {
                response('service instance not found', 404);
            }
            $_SERVER['REQUEST_METHOD'] = $task->verb;
            $_SERVER['REQUEST_URI'] = '/'.$service_instance->slug.$task->route;
            $args = [];
            foreach($task->args as $arg) {
                $args[$arg->name] = $arg->value;
            }
            $_GET = $args;
            $service_version = $service_instance->find_version();
            $exec_service->build_routes($service_instance,$service_version);
            $exec_service->build_resources($service_instance);
            $result =  $exec_service->eval_code($service_version);
            return $result;
        } else {
            return response('scheduler task not found', 404);
        }
    }




}
