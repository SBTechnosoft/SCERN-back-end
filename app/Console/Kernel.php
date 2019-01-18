<?php
namespace ERP\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Reminder::class,
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reminder')
                 ->everyMinute();
                 
        //testing...
        // $schedule->command('inspire')
        //         ->everyMinute()
        //         ->appendOutputTo(storage_path('logs/output.log'));

        // $schedule->command('reminder:cron')
        //          ->everyMinute();
       
    }
}
