<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Group;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GroupOverview extends BaseWidget
{
    protected ?string $heading = 'Estadísticas de Grupos';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $groupsByChistes = Group::leftJoin('types', 'groups.type_id', '=', 'types.id')
            ->where('types.slug', 'chistes')
            ->count();

        $groupsByAdivinanzas = Group::leftJoin('types', 'groups.type_id', '=', 'types.id')
            ->where('types.slug', 'adivinanzas')
            ->count();

        $groupsByQuiz = Group::leftJoin('types', 'groups.type_id', '=', 'types.id')
            ->where('types.slug', 'quiz')
            ->count();


        return [
            //Stat::make('Grupos', Group::count()),
            //Stat::make('Creados esta Semana', Group::where('created_at', '>=', (Carbon::now())->startOfWeek())->count()),
            //Stat::make('Creados Hoy', Group::where('created_at', '>=', (Carbon::now()->startOfDay()))->count()),

            Stat::make('Grupos de Chistes', $groupsByChistes),
            Stat::make('Grupos de Adivinanzas', $groupsByAdivinanzas),
            Stat::make('Grupos de Quiz', $groupsByQuiz),
        ];
    }
}
