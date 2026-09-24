<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueAndMembersChartWidget extends ChartWidget
{
    protected ?string $heading = 'Revenue & new members';

    protected ?string $description = 'Revenue in Rp juta, members per day';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'xl' => 2,
    ];

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp juta)',
                    'data' => [18, 22, 28, 25, 34, 42, 48.6],
                    'borderColor' => '#4F46E5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.12)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'New members',
                    'data' => [25, 29, 36, 32, 40, 44, 52],
                    'borderColor' => '#0EA5E9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => ['01 Sep', '05 Sep', '10 Sep', '15 Sep', '20 Sep', '25 Sep', '30 Sep'],
        ];
    }
}
