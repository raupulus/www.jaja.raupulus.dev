<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Suggestion;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuggestionOverview extends BaseWidget
{
    protected ?string $heading = 'Estadísticas de Sugerencias';

    protected function getStats(): array
    {
        $pending = Suggestion::whereNull('approved_at')->count();
        $approved = Suggestion::withTrashed()->whereNotNull('approved_at')->count();

        $thisMonth = Suggestion::withTrashed()
            ->where('created_at', '>=', (Carbon::now())->subDays(30))
            ->count();

        $thisWeek = Suggestion::withTrashed()
            ->where('created_at', '>=', (Carbon::now())->subDays(7))
            ->count();

        $today = Suggestion::withTrashed()
            ->where('created_at', '>=', (Carbon::now())->startOfDay())
            ->count();

        return [
            Stat::make('Total', $pending + $approved),
            Stat::make('Pendientes', $pending),
            Stat::make('Aprobadas', $approved),

            Stat::make('Último Mes', $thisMonth),
            Stat::make('Última Semana', $thisWeek),
            Stat::make('Hoy', $today),
        ];
    }
}
