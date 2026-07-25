<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendSMSCommand::class,
        \App\Console\Commands\CheckStock::class,
        \App\Console\Commands\UpdateDiscounts::class,
        \App\Console\Commands\SellPaymentDateCommand::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command('sms:send-donor')->everyFiveMinutes();
        $schedule->command('sms:send-sms')->cron('* * * * *');
        $schedule->command('stock:check')->cron('* * * * *');
        $schedule->command('discounts:update-status')->cron('* * * * *');
        $schedule->command('sells:paymentDate')->cron('* * * * *');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
