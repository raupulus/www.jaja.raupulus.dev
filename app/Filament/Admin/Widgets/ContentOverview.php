<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Content;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Contenidos', Content::count()),
            Stat::make('Creados esta Semana', Content::where('created_at', '>=', (Carbon::now())->startOfWeek())->count()),
            Stat::make('Creados Hoy', Content::where('created_at', '>=', (Carbon::now()->startOfDay()))->count()),
        ];
    }
}
