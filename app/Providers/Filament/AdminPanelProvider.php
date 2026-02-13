<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
//use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationItem;
use Filament\Pages;

class AdminPanelProvider extends PanelProvider
{
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('beranda')
        ->login()
        ->colors([
            'primary' => Color::Amber,
        ])
        // 1. Definisikan Pages secara benar (Gunakan Dashboard bawaan Filament)
        ->pages([
            \Filament\Pages\Dashboard::class, 
        ])
        // 2. Registrasi Resource (Coba panggil manual satu saja untuk tes)
        ->resources([
            \App\Filament\Resources\VisitResource::class,
        ])
        // 3. Discovery (Pencarian otomatis)
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->authPasswordBroker('users') // Memastikan dia pakai table users
        ->spa() // Coba aktifkan mode SPA (Single Page Application)
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->widgets([
            AccountWidget::class,
            FilamentInfoWidget::class,
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
        ])
        // 4. Pengaturan Sidebar
        ->sidebarCollapsibleOnDesktop()
        ->sidebarWidth('20rem')
        // 5. Menu Manual
        ->navigationItems([
            NavigationItem::make('Google Test')
                ->url('https://google.com')
                ->icon('heroicon-o-globe-alt'),
        ]);
    }
}
