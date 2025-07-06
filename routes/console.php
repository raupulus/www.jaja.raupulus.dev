<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('stats:update')->everyThirtyMinutes();
Schedule::command('sitemap:generate')->daily()->at('02:00');
Schedule::command('sitemap:content:publish-social')->everyFourHours();
