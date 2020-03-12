<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
       Commands\SendNotificationCron::class,
        Commands\ServiceNotificationCron::class,
        Commands\TaskNotificationCron::class,
        Commands\SendEventCskhCron::class,
        Commands\ReviewNotificationCron::class,
        Commands\SendSmsCron::class,
        Commands\SaveNumberCallOfDay::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
                 // ->hourly();
        //SEND NOTIFICATION FOR CUSTOMER
       $schedule->command('cron:sendnotification')
           ->dailyAt('18:00')->withoutOverlapping();
//        $schedule->command('command:servicenotification')
//            ->dailyAt('07:00')->withoutOverlapping();
        $schedule->command('command:taskNotification')
            ->dailyAt('07:30')->withoutOverlapping();
            //SEND NOTIFICATION FOR CSKH TEAM
        $schedule->command('command:SendEventCskhCron')
        ->dailyAt('07:00')->withoutOverlapping();
            //SEND REVIEW NOTIFICATION FOR EVERY MONTH
        $schedule->command('command:reviewNotification')
        ->dailyAt('08:00')->withoutOverlapping();
            //AUTO GET SELLER'S NUMBER CALL LOG
        $schedule->command('SaveNumberCallOfDay')
        ->dailyAt('12:00')->withoutOverlapping();

        $schedule->command('command:sendSmsCron')
                 ->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
