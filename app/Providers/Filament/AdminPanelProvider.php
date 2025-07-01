<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\UserContributorsChart;
use App\Http\Middleware\AdminMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
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
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                UserContributorsChart::class,
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
                //Widgets\StatsOverviewWidget::class,
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
                    //->icon('heroicon-o-pencil')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Administración')
                    //->icon('heroicon-o-pencil')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Acciones')
                    //->icon('heroicon-o-pencil')
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make('Generar Sitemap')
                ->url(fn() => route('admin.action.generate.sitemap'))
                ->icon('heroicon-o-star')
                ->group('Acciones')
                ->sort(4)
                ,
                NavigationItem::make('Limpiar Stats')
                    ->url(fn() => route('admin.action.generate.stats'))
                    ->icon('heroicon-o-play')
                    ->group('Acciones')
                    ->sort(5)
                    //->openUrlInNewTab()
                ,
            ])
            ->login()
            //->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ;
    }
}
