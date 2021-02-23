<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Carbon\Carbon;
use Cron\CronExpression;
use \App\Service;
use \App\APIInstance;
use \App\APIVersion;
use \App\Scheduler;
use \App\Libraries\Router;
use \App\Libraries\MySQLDB;
use \App\Libraries\OracleDB;
use \App\Libraries\ValidateUser;
use \App\Libraries\ExecAPI;
use Illuminate\Http\Request;

use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\InstallerCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $schedule = Scheduler::where('enabled',true)->get();
            $to_do = [];
            foreach($schedule as $task) {
                $cron = CronExpression::factory($task->cron);
                if ($cron->isDue()) {
                    $to_do[] = $task;
                }
            }
            foreach($to_do as $task) {
                // Look at forking processes for these...
                // http://php.net/manual/en/function.pcntl-fork.php
                // tjc 10/11/18
                $current_minute = Carbon::now()->format("Y-m-d H:i:00");
                if (is_null(Scheduler::where('id',$task->id)->where('last_exec_cron',$current_minute)->first())) {
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
                    $api_version = $api_instance->find_version();
                    $exec_api->build_routes($api_instance,$api_version);
                    $exec_api->build_resources($api_instance);
                    $result =  $exec_api->eval_code($api_instance, $api_version);
                    $task->last_response = $result;
                    $task->last_exec_stop = Carbon::now()->format("Y-m-d H:i:s");
                    $task->update();
                    var_dump($result);
                } else {
                    echo "Already Run... Skipping\n";
                }
            }
            
        })->everyMinute();
    }
}
