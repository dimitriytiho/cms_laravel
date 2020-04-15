<?php

namespace App\Console;

use App\Helpers\Upload;
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
        // $schedule->command('inspire')->hourly();


        // Обновление веб-сайта
        $schedule->call(function () {
            Upload::getUpload();
        })->monthlyOn(1, '02:00');

        // Резервное копирование веб-сайта
        //$schedule->command('backup:clean')->monthlyOn(1, '02:05');
        //$schedule->command('backup:run')->monthlyOn(1, '02:10');
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
