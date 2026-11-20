<?php

namespace App\Filament\Admin\Widgets;

use App\Helpers\StatsHelper;
use Filament\Widgets\ChartWidget;

class UserContributorsChart extends ChartWidget
{
    protected static ?string $heading = 'Mayores Contribuciones';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    /**
     * Paleta fija y accesible de colores para la gráfica circular.
     *
     * @var list<string>
     */
    public const PALETTE = [
        '#00bcd4',
        '#f59e0b',
        '#10b981',
        '#6366f1',
        '#ec4899',
        '#14b8a6',
        '#f97316',
        '#8b5cf6',
        '#84cc16',
        '#06b6d4',
    ];

    protected function getData(): array
    {
        $contributors = StatsHelper::getUsersMoreActive(10);

        $data = [];
        $labels = [];
        $colors = [];

        foreach ($contributors as $index => $contributor) {
            $data[] = $contributor['total'];
            $labels[] = '@'.$contributor['nick'];
            $colors[] = self::PALETTE[$index % count(self::PALETTE)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Contribuciones por usuario',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
