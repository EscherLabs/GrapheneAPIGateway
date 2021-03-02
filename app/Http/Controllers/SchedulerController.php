<?php

namespace App\Http\Controllers;

use Cron\CronExpression;
use \App\API;
use \App\APIInstance;
use \App\APIVersion;
use \App\APIUser;
use \App\DatabaseInstance;
use \App\Scheduler;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use \App\Libraries\ExecAPI;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class SchedulerController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Scheduler::with(['api_instance'=>function($query){
            $query->with('environment');
        }])->orderby('name')->get();
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
            $task->last_exec_start = Carbon::now()->format("Y-m-d H:i:s");
            $task->update();

            $exec_api = new ExecAPI();
            $api_instance = APIInstance::where('id',$task->api_instance_id)->with('api')->first();    
            if (is_null($api_instance)) {
                response('api instance not found', 404);
            }
            $_SERVER['REQUEST_METHOD'] = $task->verb;
            $_SERVER['REQUEST_URI'] = '/'.$api_instance->slug.$task->route;
            $args = [];
            foreach($task->args as $arg) {
                $args[$arg->name] = $arg->value;
            }
            $_GET = $args;
            $api_version = $api_instance->find_version();
            $exec_api->build_routes($api_instance,$api_version);
            $exec_api->build_resources($api_instance);
            $result =  $exec_api->eval_code($api_instance, $api_version);

            $task->last_response = $result;
            $task->last_exec_stop = Carbon::now()->format("Y-m-d H:i:s");
            $task->update();

            return $result;
        } else {
            return response('scheduler task not found', 404);
        }
    }




}
