<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\Heartbeat;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CustomNotificationCommand::class,
        Commands\ChildBirtdayNotificationCommand::class,
        Commands\CustomerRegistrationNotificationCommand::class,
        Commands\ChildEDDNotificationCommand::class,
        Commands\ChildDOBNotificationCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Custom notification
        $schedule->command('activitylog:clean')->daily();      
        // $schedule->job(new SendNewCustomer)->cron(M, H, D, M, Y);
        $schedule->command('ChildBirtdayNotificationCommand')->dailyAt('17:06');
        $schedule->command('CustomerRegistrationNotificationCommand')->dailyAt('17:30');
        $schedule->command('ChildEDDNotificationCommand')->dailyAt('18:00');
        $schedule->command('ChildDOBNotificationCommand')->dailyAt('18:30');
        // $schedule->command('ChildBirtdayNotificationCommand')->everyTwoMinutes();

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
