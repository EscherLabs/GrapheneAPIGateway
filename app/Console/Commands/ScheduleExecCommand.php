<?php

namespace App\Console\Commands;

use App\Environment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Scheduler;
use Carbon\Carbon;
use \App\Libraries\ExecAPI;
use \App\APIInstance;
use \App\APIVersion;

class ScheduleExecCommand extends Command
{
    protected $config = [];
    protected $signature = 'schedule:exec {id}';
    protected $description = 'Runs Specified Schedule By ID';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $schedule_id = $this->argument('id');
        $task = Scheduler::where('id',$schedule_id)->first();
        if (is_null($task)) {
            echo 'Scheduled Task not found'."\n"; exit();
        }

        $current_minute = Carbon::now()->format("Y-m-d H:i:00");
        // if (is_null(Scheduler::where('id',$task->id)->where('last_exec_cron',$current_minute)->first())) {
            $task->last_exec_cron = $current_minute;
            $task->last_exec_start = Carbon::now()->format("Y-m-d H:i:s");
            $task->update();

            $exec_api = new ExecAPI();
            $api_instance = APIInstance::where('id',$task->api_instance_id)->with('api')->first();    
            if (is_null($api_instance)) {
                echo 'api instance not found'; exit();
            }
            $_SERVER['REQUEST_METHOD'] = $task->verb;
            $_SERVER['REQUEST_URI'] = '/'.$api_instance->slug.$task->route;
            $args = [];
            if (isset($task->args) && is_array($task->args)) {
                foreach($task->args as $arg) {
                    $args[$arg->name] = $arg->value;
                }
            }
            $_GET = $args;
            $result = $exec_api->eval_code($api_instance);
            $task->last_response = $result;
            $task->last_exec_stop = Carbon::now()->format("Y-m-d H:i:s");
            $task->update();
            var_dump($result);
        // } else {
        //     echo "Already Run... Skipping\n";
        // }
    }
}
