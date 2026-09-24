<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('TradingEdu')
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->font('Inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap')
            ->maxContentWidth(Width::Full)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): string => '
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        :root, body, [class*="fi-"] {
                            font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
                        }
                    </style>
                '
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => '
                    <button
                        type="button"
                        x-data
                        x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                        class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 transition -ml-1 mr-3 shrink-0"
                        title="Buka / Tutup Sidebar"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                        </svg>
                    </button>
                '
            )
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17.5rem')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('USER MANAGEMENT')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make()
                    ->label('LEARNING')
                    ->icon('heroicon-o-academic-cap'),
                NavigationGroup::make()
                    ->label('MARKET')
                    ->icon('heroicon-o-chart-bar'),
                NavigationGroup::make()
                    ->label('COMMUNITY')
                    ->icon('heroicon-o-chat-bubble-left-right'),
                NavigationGroup::make()
                    ->label('MONETIZATION')
                    ->icon('heroicon-o-banknotes'),
                NavigationGroup::make()
                    ->label('CONTENT')
                    ->icon('heroicon-o-newspaper'),
                NavigationGroup::make()
                    ->label('WEBSITE')
                    ->icon('heroicon-o-globe-alt'),
                NavigationGroup::make()
                    ->label('SETTINGS')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
