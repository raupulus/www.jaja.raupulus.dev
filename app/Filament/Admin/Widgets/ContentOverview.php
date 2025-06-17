<?php

namespace App\Filament\Admin\Widgets;

use App\Helpers\StatsHelper;
use App\Models\Content;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends BaseWidget
{
    protected ?string $heading = 'Estadísticas de Contenidos';

    protected function getStats(): array
    {
        ## Array para estadísticas de contenidos creados en el último mes
        $contentsChartLastTrend = Content::select(\DB::raw('count(*) as total'))
            ->where('created_at', '>=', (Carbon::now())->subMonths(12))
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->pluck('total')
            ->toArray()
        ;

        $contentsChartLastWeek = Content::select(\DB::raw('count(*) as total'))
            ->where('created_at', '>=', (Carbon::now())->subDays(7))
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->pluck('total')
            ->toArray()
        ;

        $contentsChartLastDays = Content::select(\DB::raw('count(*) as total'))
            ->where('created_at', '>=', (Carbon::now())->subDays(3))
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->pluck('total')
            ->toArray()
        ;

        $chistesTotal = Content::leftJoin('groups', 'groups.id', '=', 'contents.group_id')
            ->leftJoin('types', 'types.id', '=', 'groups.type_id')
            ->where('types.slug', 'chistes')
            ->count();

        $chistesChart = Content::select(\DB::raw('count(*) as total'))
            ->leftJoin('groups', 'groups.id', '=', 'contents.group_id')
            ->leftJoin('types', 'types.id', '=', 'groups.type_id')
            ->where('types.slug', 'chistes')
            ->where('contents.created_at', '>=', (Carbon::now())->subMonths(12))
            ->groupBy(\DB::raw('DATE(contents.created_at)'))
            ->pluck('total')
            ->toArray();

        $adivinanzasChart = Content::select(\DB::raw('count(*) as total'))
            ->leftJoin('groups', 'groups.id', '=', 'contents.group_id')
            ->leftJoin('types', 'types.id', '=', 'groups.type_id')
            ->where('types.slug', 'adivinanzas')
            ->where('contents.created_at', '>=', (Carbon::now())->subMonths(12))
            ->groupBy(\DB::raw('DATE(contents.created_at)'))
            ->pluck('total')
            ->toArray();

        $quizChart = Content::select(\DB::raw('count(*) as total'))
            ->leftJoin('groups', 'groups.id', '=', 'contents.group_id')
            ->leftJoin('types', 'types.id', '=', 'groups.type_id')
            ->where('types.slug', 'quiz')
            ->where('contents.created_at', '>=', (Carbon::now())->subMonths(12))
            ->groupBy(\DB::raw('DATE(contents.created_at)'))
            ->pluck('total')
            ->toArray();

        $adivinanzasTotal = Content::leftJoin('groups', 'groups.id', '=', 'contents.group_id')
            ->leftJoin('types', 'types.id', '=', 'groups.type_id')
            ->where('types.slug', 'adivinanzas')
            ->count();

        $quizTotal = Content::leftJoin('groups', 'groups.id', '=', 'contents.group_id')
            ->leftJoin('types', 'types.id', '=', 'groups.type_id')
            ->where('types.slug', 'quiz')
            ->count();

        return [
            Stat::make('Contenidos', Content::count())
                ->chart($contentsChartLastTrend),
            Stat::make('Creados esta Semana', Content::where('created_at', '>=', (Carbon::now())->startOfWeek())->count())
                ->chart($contentsChartLastWeek),
            Stat::make('Creados Hoy', Content::where('created_at', '>=', (Carbon::now()->startOfDay()))->count())
                ->chart($contentsChartLastDays),
            Stat::make('Chistes', $chistesTotal)
                ->chart($chistesChart),
            Stat::make('Adivinanzas', $adivinanzasTotal)
                ->chart($adivinanzasChart),
            Stat::make('Quiz', $quizTotal)
                ->chart($quizChart),
        ];
    }
}
