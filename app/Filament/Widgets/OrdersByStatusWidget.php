<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrdersByStatusWidget extends ChartWidget
{
    protected ?string $heading = 'Orders by status';

    protected ?string $description = 'Total: 2.406 orders';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'xl' => 1,
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Persentase',
                    'data' => [68, 14, 7, 11],
                    'backgroundColor' => [
                        '#10B981', // Paid
                        '#F59E0B', // Pending
                        '#EF4444', // Failed or expired
                        '#6366F1', // Refunded
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                'Paid (68%)',
                'Pending (14%)',
                'Failed/expired (7%)',
                'Refunded (11%)',
            ],
        ];
    }
}
