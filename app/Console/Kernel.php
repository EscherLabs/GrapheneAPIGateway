<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Carbon\Carbon;
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

use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
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
            $schedule = Scheduler::all();
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
                    $exec_service = new ExecService();
                    $service_instance = ServiceInstance::where('id',$task->service_instance_id)->with('Service')->first();
                    if (is_null($service_instance)) {
                        abort(404);
                    }
                    $_SERVER['REQUEST_METHOD'] = $task->verb;
                    $_SERVER['REQUEST_URI'] = '/'.$service_instance->slug.$task->route;
                    $args = [];
                    if (isset($task->args) && is_array($task->args)) {
                        foreach($task->args as $arg) {
                            $args[$arg->name] = $arg->value;
                        }
                    }
                    $_GET = $args;
                    $service_version = $service_instance->find_version();
                    $exec_service->build_routes($service_instance,$service_version);
                    $exec_service->build_resources($service_instance);
                    $result =  $exec_service->eval_code($service_version);
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
