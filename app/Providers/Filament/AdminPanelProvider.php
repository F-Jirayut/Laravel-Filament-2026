<?php

namespace App\Providers\Filament;

use App\Models\Menu;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
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
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])

            // ⭐ Dynamic Sidebar จาก Database พร้อมตรวจสอบสิทธิ์
            // ->navigationItems($this->getNavigationItems())

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

    /**
     * ดึงเมนูจากฐานข้อมูลและตรวจสอบสิทธิ์ด้วย Spatie
     */
    protected function getNavigationItems(): array
    {
        // ป้องกัน error ในกรณี migrate หรือยังไม่ได้ login
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();

        return Menu::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($menu) use ($user) {
                // ถ้าไม่มี permission ให้เข้าถึงได้
                if (empty($menu->permission_name)) {
                    return true;
                }

                return $user->can($menu->permission_name);
            })
            ->map(function ($menu) {
                return NavigationItem::make($menu->name)
                    ->icon($menu->icon ?? 'heroicon-o-document-text')
                    ->url(url($menu->route))
                    ->sort($menu->sort_order);
            })
            ->toArray();
    }
}