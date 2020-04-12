<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\WebsitePush;

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
        //定时执行提交链接
        $schedule->call(function () {
            $website_pushes = WebsitePush::where([
                ['is_automatic','=',true],
                ['status','<>',1],
            ])->get();
            foreach($website_pushes as $website_push){
                $website_push->automatic();
            }
        })->daily(env('WEBSITE_PUSH_START','8:00'));
        //定时关闭提交链接
        $schedule->call(function () {
            $website_pushes = WebsitePush::where([
                ['is_automatic','=',true],
                ['status','=',1],
            ])->update([
                'status'=>2
            ]);
        })->daily(env('WEBSITE_PUSH_END','23:55'));
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
