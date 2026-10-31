<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dasbor;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
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
            ->profile(isSimple: false)
            ->userMenuItems(['profile' => fn (\Filament\Actions\Action $action) => $action->visible(fn (): bool => ! auth()->user()?->isGuestVisitor())])
            ->brandName(config('promise.brand_name'))
            ->brandLogo(asset('images/promise-logo.svg').'?v=2')
            ->darkModeBrandLogo(asset('images/promise-logo-dark.svg').'?v=2')
            ->brandLogoHeight('2.2rem')
            ->favicon(asset('images/favicon.svg'))
            ->colors([
                'primary' => Color::generateV3Palette('#0F5E9C'),
                'danger' => Color::generateV3Palette('#D32F2F'),
                'warning' => Color::generateV3Palette('#D98E04'),
                'success' => Color::generateV3Palette('#2E7D4F'),
                'info' => Color::generateV3Palette('#2A8C9E'),
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->navigationGroups([
                NavigationGroup::make('Umum'),
                NavigationGroup::make('Integrasi'),
                NavigationGroup::make('Ruang Lingkup'),
                NavigationGroup::make('Waktu'),
                NavigationGroup::make('Biaya'),
                NavigationGroup::make('Mutu')->collapsed(),
                NavigationGroup::make('Sumber Daya Manusia')->collapsed(),
                NavigationGroup::make('Komunikasi')->collapsed(),
                NavigationGroup::make('Risiko')->collapsed(),
                NavigationGroup::make('Pengadaan')->collapsed(),
                NavigationGroup::make('Administrasi')->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dasbor::class])
            ->widgets([])
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn () => Blade::render('<x-promise.guest-login />'))
            ->renderHook(PanelsRenderHook::CONTENT_START, fn () => Blade::render('<x-promise.company-bar /><x-promise.guest-banner />'))
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn () => Blade::render('<x-promise.help-button />'))
            ->renderHook(PanelsRenderHook::FOOTER, fn () => Blade::render('<x-promise.footer />'))
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
