<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Wallo\FilamentCompanies\Pages\User\PersonalAccessTokens;
use Wallo\FilamentCompanies\Pages\User\Profile;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Auth;
use Wallo\FilamentCompanies\Pages\Company\CompanySettings;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            // ->login()
            // ->registration()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Profile::class,
                PersonalAccessTokens::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->icon('heroicon-o-user-circle')
                    ->url(static fn() => url(Profile::getUrl())),
                MenuItem::make()
                    ->label('Files')
                    ->icon('heroicon-m-folder')
                    ->url(static fn() => url(\App\Filament\Resources\IFileResource\Pages\ManageIFiles::getUrl(panel: 'dashboard', tenant: Auth::user()->personalCompany()))),
                MenuItem::make()
                    ->label('Company')
                    ->icon('heroicon-o-building-office')
                    ->url(static fn() => url(CompanySettings::getUrl(panel: 'company', tenant: Auth::user()->currentCompany()->first()))),
            ])
            ->navigationItems([
                /* NavigationItem::make('Personal Access Tokens')
                    ->label(static fn(): string => __('filament-companies::default.navigation.links.tokens'))
                    ->icon('heroicon-o-key')
                    ->url(static fn() => url(PersonalAccessTokens::getUrl())), */
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
