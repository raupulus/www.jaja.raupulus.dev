<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('stats:update')->everyThirtyMinutes();
Schedule::command('sitemap:generate')->daily()->at('02:00');
Schedule::command('content:publish-social')->dailyAt('07:00');
Schedule::command('content:publish-social')->dailyAt('12:00');
Schedule::command('content:publish-social')->dailyAt('18:00');
