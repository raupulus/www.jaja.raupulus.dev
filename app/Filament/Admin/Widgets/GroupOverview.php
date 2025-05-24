<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Group;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GroupOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Grupos', Group::count()),
            Stat::make('Creados esta Semana', Group::where('created_at', '>=', (Carbon::now())->startOfWeek())->count()),
            Stat::make('Creados Hoy', Group::where('created_at', '>=', (Carbon::now()->startOfDay()))->count()),
        ];
    }
}
