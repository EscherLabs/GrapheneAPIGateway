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
            // MySchedules -- Move to DB at some point!
            $schedule = [
                ['cron'=>'* * * * *','service_instance_id'=>'1'],
            ];
            $schedule = Scheduler::all();
            foreach($schedule as $task) {
                $cron = CronExpression::factory($task->cron);
                if ($cron->isDue()) {
                    echo "do a thing!";
                    $exec_service = new ExecService();
                    $service_instance = ServiceInstance::where('id',$task->service_instance_id)->with('Service')->first();
                    if (is_null($service_instance)) {
                        abort(404);
                    }
                    $_SERVER['REQUEST_URI'] = '/'.$service_instance->slug.'/'.$task->route;
                    $_POST = $task->$args;
                    $service_version = ServiceVersion::where('id',$service_instance->service_version_id)->first();
                    $exec_service->build_routes($service_instance,$service_version,$service_instance->slug);
                    $exec_service->build_resources($service_instance);
                    return $exec_service->eval_code($service_version);
                }
            }
            
        })->everyMinute();
    }
}
