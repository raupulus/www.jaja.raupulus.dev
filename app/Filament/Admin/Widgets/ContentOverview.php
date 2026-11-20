<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Content;
use App\Services\CacheInvalidationService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ContentOverview extends BaseWidget
{
    protected ?string $heading = 'Estadísticas de Contenidos';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $metrics = Cache::remember(CacheInvalidationService::ADMIN_CONTENT_OVERVIEW_KEY, 300, function (): array {
            $contentsChartLastTrend = Content::select(\DB::raw('count(*) as total'))
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy(\DB::raw('DATE(created_at)'))
                ->pluck('total')
                ->toArray();

            $contentsChartLastWeek = Content::select(\DB::raw('count(*) as total'))
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy(\DB::raw('DATE(created_at)'))
                ->pluck('total')
                ->toArray();

            $contentsChartLastDays = Content::select(\DB::raw('count(*) as total'))
                ->where('created_at', '>=', Carbon::now()->subDays(3))
                ->groupBy(\DB::raw('DATE(created_at)'))
                ->pluck('total')
                ->toArray();

            $chistesTotal = Content::leftJoin('groups', 'groups.id', '=', 'contents.group_id')
                ->leftJoin('types', 'types.id', '=', 'groups.type_id')
                ->where('types.slug', 'chistes')
                ->count();

            $chistesChart = Content::select(\DB::raw('count(*) as total'))
                ->leftJoin('groups', 'groups.id', '=', 'contents.group_id')
                ->leftJoin('types', 'types.id', '=', 'groups.type_id')
                ->where('types.slug', 'chistes')
                ->where('contents.created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy(\DB::raw('DATE(contents.created_at)'))
                ->pluck('total')
                ->toArray();

            $adivinanzasChart = Content::select(\DB::raw('count(*) as total'))
                ->leftJoin('groups', 'groups.id', '=', 'contents.group_id')
                ->leftJoin('types', 'types.id', '=', 'groups.type_id')
                ->where('types.slug', 'adivinanzas')
                ->where('contents.created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy(\DB::raw('DATE(contents.created_at)'))
                ->pluck('total')
                ->toArray();

            $quizChart = Content::select(\DB::raw('count(*) as total'))
                ->leftJoin('groups', 'groups.id', '=', 'contents.group_id')
                ->leftJoin('types', 'types.id', '=', 'groups.type_id')
                ->where('types.slug', 'quiz')
                ->where('contents.created_at', '>=', Carbon::now()->subMonths(12))
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
                'total' => Content::count(),
                'week' => Content::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
                'today' => Content::where('created_at', '>=', Carbon::now()->startOfDay())->count(),
                'chistesTotal' => $chistesTotal,
                'adivinanzasTotal' => $adivinanzasTotal,
                'quizTotal' => $quizTotal,
                'contentsChartLastTrend' => $contentsChartLastTrend,
                'contentsChartLastWeek' => $contentsChartLastWeek,
                'contentsChartLastDays' => $contentsChartLastDays,
                'chistesChart' => $chistesChart,
                'adivinanzasChart' => $adivinanzasChart,
                'quizChart' => $quizChart,
            ];
        });

        return [
            Stat::make('Contenidos', $metrics['total'])
                ->chart($metrics['contentsChartLastTrend']),
            Stat::make('Creados esta Semana', $metrics['week'])
                ->chart($metrics['contentsChartLastWeek']),
            Stat::make('Creados Hoy', $metrics['today'])
                ->chart($metrics['contentsChartLastDays']),
            Stat::make('Chistes', $metrics['chistesTotal'])
                ->chart($metrics['chistesChart']),
            Stat::make('Adivinanzas', $metrics['adivinanzasTotal'])
                ->chart($metrics['adivinanzasChart']),
            Stat::make('Quiz', $metrics['quizTotal'])
                ->chart($metrics['quizChart']),
        ];
    }
}
