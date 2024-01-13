<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $files = Storage::disk('public')->files('files');
            foreach ($files as $file) {
                if (Storage::disk('public')->lastModified($file) < now()->subDays(14)->getTimestamp()) {
                    Storage::disk('public')->delete($file);
                }
            }
        })
        ->dailyAt('2:30')
        ->emailOutputOnFailure('martin.j.osborne@gmail.com');
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
