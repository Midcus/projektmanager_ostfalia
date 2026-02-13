<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CleanExpiredInterests::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('interests:clean-expired')
                 ->daily()
                 ->appendOutputTo(storage_path('logs/cron.log'))
                 ->onSuccess(fn () => Log::info('interests:clean-expired OK'))
                 ->onFailure(fn () => Log::error('interests:clean-expired FAILED'));
    }


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}