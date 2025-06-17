<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Content;
use App\Models\Group;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected ?string $heading = 'Estadísticas de Usuarios';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $users = User::count();
        $uploaders = Content::distinct('uploaded_by')->count();

        return [
            Stat::make('Total', $users + $uploaders),
            Stat::make('Registrados', $uploaders),
            Stat::make('Uploaders', $users),
        ];
    }
}
