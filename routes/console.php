<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



//TODO in hostinger * * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1
//php artisan schedule:list
// result - queue:work --once --timeout=0    Every minute    Next Due: in 1 minute
//php artisan schedule:run
app()->booted(function () {
    app(Schedule::class)
        ->exec('php artisan queue:work --once --timeout=0 --sleep=1 --tries=1')
        ->everyMinute()
        ->withoutOverlapping();
});
