<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStatsWidget;
use App\Filament\Widgets\LatestOrdersWidget;
use App\Filament\Widgets\OrdersByStatusWidget;
use App\Filament\Widgets\RevenueAndMembersChartWidget;
use App\Filament\Widgets\UpcomingSessionsAndApprovalWidget;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getColumns(): int | array
    {
        return 12;
    }

    public function getWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
            RevenueAndMembersChartWidget::class,
            OrdersByStatusWidget::class,
            LatestOrdersWidget::class,
            UpcomingSessionsAndApprovalWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('today')->label('Today'),
                Action::make('last_7_days')->label('Last 7 days'),
                Action::make('last_30_days')->label('Last 30 days'),
                Action::make('this_month')->label('This month'),
                Action::make('this_year')->label('This year'),
            ])
                ->label('Last 30 days')
                ->icon('heroicon-m-calendar')
                ->color('gray')
                ->button()
                ->outlined(),

            Action::make('export_report')
                ->label('Export report')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('primary')
                ->action(function () {
                    Notification::make()
                        ->title('Laporan Digenerate')
                        ->body('Data analitik 30 hari terakhir berhasil diexport ke format Excel / CSV.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
