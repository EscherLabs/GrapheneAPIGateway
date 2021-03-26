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
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

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
        $task = Scheduler::select('id')->where('id',$scheduler_id)->first();
        if (!is_null($task)) {
            $exitCode = Artisan::call('schedule:exec', ['id' => $scheduler_id]);
            $task = Scheduler::where('id',$scheduler_id)->first();
            return $task->last_response;
        } else {
            return response('scheduler task not found', 404);
        }
    }




}
