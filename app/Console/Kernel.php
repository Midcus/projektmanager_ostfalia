<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
                 ->onSuccess(function () {
                     file_put_contents(
                         '/home/khoituanh/cron-error.log',
                         '[' . now()->toDateTimeString() . '] interests:clean-expired executed successfully' . PHP_EOL,
                         FILE_APPEND
                     );
                 })
                 ->onFailure(function () {
                     file_put_contents(
                         '/home/khoituanh/cron-error.log',
                         '[' . now()->toDateTimeString() . '] interests:clean-expired failed' . PHP_EOL,
                         FILE_APPEND
                     );
                 });
    }


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}