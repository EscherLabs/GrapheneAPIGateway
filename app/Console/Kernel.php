<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
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
            foreach($schedule as $task) {
                $cron = CronExpression::factory($task->cron);
                if ($cron->isDue()) {
                    $exec_service = new ExecService();
                    $service_instance = ServiceInstance::where('id',$task->service_instance_id)->with('Service')->first();
                    if (is_null($service_instance)) {
                        abort(404);
                    }
                    $_SERVER['REQUEST_METHOD'] = $task->verb;
                    $_SERVER['REQUEST_URI'] = '/'.$service_instance->slug.$task->route;
                    $args = [];
                    foreach($task->args as $arg) {
                        $args[$arg->name] = $arg->value;
                    }
                    $_GET = $args;
                    $service_version = ServiceVersion::where('id',$service_instance->service_version_id)->first();
                    $exec_service->build_routes($service_instance,$service_version,$service_instance->slug);
                    $exec_service->build_resources($service_instance);
                    $result =  $exec_service->eval_code($service_version);
                    var_dump($result);
                }
            }
            
        })->everyMinute();
    }
}
