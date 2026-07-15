<?php

namespace App\Console;

use App\Console\Commands\CreateAdminUser;
use App\Console\Commands\CreateDummySalesData;
use App\Console\Commands\DeleteDummySalesData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        CreateAdminUser::class,
        CreateDummySalesData::class,
        DeleteDummySalesData::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        //
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
