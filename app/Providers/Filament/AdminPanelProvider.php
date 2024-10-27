<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\GraficoResultadosAlcalde;
use App\Filament\Admin\Widgets\GraficoResultadosPacto;
use App\Filament\Admin\Widgets\TablaResultadosAlcalde;
use App\Filament\Admin\Widgets\TablaResultadosConcejales;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
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
            ->brandName('Elecciones 2024')
            ->colors([
                'primary' => Color::Fuchsia,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Candidatos')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make()
                    ->label('Coaliciones')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make()
                    ->label('Votación')
                    ->icon('heroicon-o-archive-box'),
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                TablaResultadosAlcalde::class,
                GraficoResultadosAlcalde::class,
                TablaResultadosConcejales::class,
                GraficoResultadosPacto::class,
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
            ]);
    }
}
