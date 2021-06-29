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
            $this->error('Scheduled Task Not Found!'); exit();
        }
        if ($task->enabled !== true) {
            $this->error('Scheduled Task "'.$task->name.'" is not Enabled... skipping'); exit();
        }

        $current_minute = Carbon::now()->format("Y-m-d H:i:00");
        $task->last_exec_cron = $current_minute;
        $task->last_exec_start = Carbon::now()->format("Y-m-d H:i:s");
        $task->update();

        $exec_api = new ExecAPI();
        $api_instance = APIInstance::where('id',$task->api_instance_id)->with('api')->first();    
        if (is_null($api_instance)) {
            $this->error('API Instance Not Found!'); exit();
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
    }
}
