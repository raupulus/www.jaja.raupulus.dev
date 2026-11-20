<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('stats:update')->everyThirtyMinutes()->withoutOverlapping();
Schedule::command('sitemap:generate')->daily()->at('02:00')->withoutOverlapping();
Schedule::command('content:publish-social')->dailyAt('07:00')->withoutOverlapping();
Schedule::command('content:publish-social')->dailyAt('12:00')->withoutOverlapping();
Schedule::command('content:publish-social')->dailyAt('18:00')->withoutOverlapping();
