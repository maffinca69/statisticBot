<?php

namespace App\Console;

use App\Console\Commands\DailyStatisticCommand;
use App\Console\Commands\RefreshToken;
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
        DailyStatisticCommand::class,
        RefreshToken::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('statistic:send')
            ->timezone(env('APP_TIMEZONE'))
            ->weekends() // игнорируем выходные. Пока отключено для тестирования
            ->twiceDaily(11, 19); // каждый день в 11:00 и 19:00
    }
}
