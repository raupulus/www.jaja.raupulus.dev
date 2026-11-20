<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard as AdminDashboard;
use App\Filament\Admin\Widgets\UserContributorsChart;
use App\Http\Middleware\AdminMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::hex('#0e7490'),
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                AdminDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                UserContributorsChart::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                // Widgets\StatsOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                AdminMiddleware::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Contenidos')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Moderación')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Administración')
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make('Ir a mi Panel')
                    ->url(fn (): string => route('filament.panel.pages.dashboard'))
                    ->icon('heroicon-o-squares-2x2')
                    ->sort(90),
                NavigationItem::make('Ver web pública')
                    ->url(fn (): string => route('index'))
                    ->icon('heroicon-o-globe-alt')
                    ->openUrlInNewTab()
                    ->sort(95),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Ir a mi Panel')
                    ->url(fn (): string => route('filament.panel.pages.dashboard'))
                    ->icon('heroicon-o-squares-2x2'),
                MenuItem::make()
                    ->label('Ver web pública')
                    ->url(fn (): string => route('index'))
                    ->icon('heroicon-o-globe-alt')
                    ->openUrlInNewTab(),
            ])
            ->login()
            // ->registration()
            // ->passwordReset()
            ->emailVerification()
            ->profile();
    }
}
