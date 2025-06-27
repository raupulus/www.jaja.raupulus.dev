<?php

namespace App\Filament\Admin\Widgets;

use App\Helpers\StatsHelper;
use Filament\Widgets\ChartWidget;

class UserContributorsChart extends ChartWidget
{
    protected static ?string $heading = 'Mayores Contribuciones';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $contributors = StatsHelper::getUsersMoreActive(10);

        $data = [];
        $labels = [];
        $colors = [];

        foreach ($contributors as $contributor) {
            $data[] = $contributor['total'];
            $labels[] = '@' . $contributor['nick'];
            $colors[] = $this->generateRandomColor();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Contribuciones por usuario',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    //'borderColor' => $colors,
                    //'borderWidth' => 1,

                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        //https://filamentphp.com/docs/3.x/widgets/charts
        return 'pie';
    }

    /**
     * Genera un color hexadecimal aleatorio
     */
    private function generateRandomColor(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

}
