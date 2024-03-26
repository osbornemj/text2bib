<?php

namespace App\Console;

use Carbon\Carbon;

use DB;
use App\Models\Conversion;
use App\Models\Output;
use App\Models\Statistic;
        
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
        // Delete files more than 7 days old
        $schedule->call(function () {
            $files = Storage::disk('public')->files('files');
            foreach ($files as $file) {
                $time = Storage::disk('public')->lastModified($file);
                $fileModifiedDateTime = Carbon::parse($time);
                
                if (Carbon::now()->gt($fileModifiedDateTime->addDays(7))) {
                    //echo $file."<br>";
                    Storage::disk("public")->delete($file);
                }
            }
        })
        ->dailyAt('2:30')
        ->emailOutputOnFailure(config('app.job_failure_email'));

        // Write stats
        $schedule->call(function () {
            $input['stat_date'] = Carbon::yesterday()->format('Y-m-d');
            $input['user_count'] = DB::table('conversions')->whereDate('created_at', $input['stat_date'])->distinct('user_id')->count();
            $input['conversion_count'] = Conversion::whereDate('created_at', $input['stat_date'])->count();
            $input['item_count'] = Output::whereDate('created_at', $input['stat_date'])->count();
            Statistic::create($input);
        })
        ->dailyAt('12:00') // not at midnight, to avoid timezone difference between cron job time and created_at times.
        ->emailOutputOnFailure(config('app.job_failure_email'));
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
