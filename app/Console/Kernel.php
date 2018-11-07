<?php

namespace App\Console;

use App\Console\Commands\InsertZhBookImf;
use App\Console\Commands\UpdateBookTopic;
use Illuminate\Console\Scheduling\Schedule;
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
        \Laravelista\LumenVendorPublish\VendorPublishCommand::class,
        'translate:topic'=>UpdateBookTopic::class,
        'InsertZhBookImf'=>InsertZhBookImf::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
