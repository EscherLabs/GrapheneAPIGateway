<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Cron\CronExpression;
use \App\Module;
use \App\ModuleInstance;
use \App\ModuleVersion;
use \App\APIUser;
use \App\DatabaseInstance;
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
            foreach($schedule as $task) {
                $cron = CronExpression::factory($task['cron']);
                if ($cron->isDue()) {
                    echo "do a thing!";
                    $exec_service = new ExecService();

                    $module_instance = ModuleInstance::where('id',$task['service_instance_id'])->with('module')->first();
                    if (is_null($module_instance)) {
                        abort(404);
                    }
                    $module_version = ModuleVersion::where('id',$module_instance->module_version_id)->first();
                    $exec_service->build_routes($module_instance,$module_version,$module_instance->slug);
                    // $users_arr = $exec_service->build_permissions($module_instance,$module_instance->slug);
                    // ValidateUser::assert_valid_user($users_arr); // Bail if user is invalid!
                    $exec_service->build_resources($module_instance);
                    return $exec_service->eval_code($module_version);
                            }
            }
            
        })->everyMinute();
    }
}
