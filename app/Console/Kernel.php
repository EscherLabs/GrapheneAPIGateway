<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Schema;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\InstallerCommand::class,
        \App\Console\Commands\ScheduleExecCommand::class,
        \App\Console\Commands\APICall::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Only run scheduled tasks if the database is connected and the scheduler table exists
        try {
            DB::connection()->getPdo();
            if (Schema::hasTable('scheduler')) {
                $tasks = DB::table('scheduler')->select('id','cron')->get();
                foreach($tasks as $task) {
                    $schedule->command('schedule:exec '.$task->id)
                        ->cron($task->cron)
                        ->onOneServer()
                        ->runInBackground();
                }
            }    
        } catch (\Exception $e) {
            // Do nothing... we don't really care
        }
    }

}