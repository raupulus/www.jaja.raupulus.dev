<?php

namespace App\Filament\Panel\Widgets;

use App\Models\Content;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Contenidos', Content::where('user_id', auth()->id())->count()),
            Stat::make('Creados esta Semana', Content::where('user_id', auth()->id())
                ->where('created_at', '>=', (Carbon::now())->startOfWeek())
                ->count()),
            Stat::make('Creados Hoy', Content::where('user_id', auth()->id())
                ->where('created_at', '>=', (Carbon::now()->startOfDay()))
                ->count()),
        ];
    }
}
