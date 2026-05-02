<?php

namespace App\Providers\Filament;

use App\Http\Middleware\CheckMenuActivation;
use App\Models\Menu;
use App\Models\User;
use Filament\Facades\Filament;
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
                CheckMenuActivation::class
            ]);
    }

    public function boot(): void
    {
        // ใช้ Filament::serving เพื่อรอให้ระบบ Auth พร้อมก่อน
        Filament::serving(function () {
            Filament::getCurrentPanel()->navigationItems(
                $this->generateDynamicNavigation()
            );
        });
    }

    private function generateDynamicNavigation(): array
    {
        // ดึงข้อมูลเมนูที่ active และเรียงลำดับ
        // คุณอาจจะดึงเฉพาะเมนูที่ไม่มี parent_id ถ้าต้องการทำ Dropdown (แต่ Filament Panel v3 เน้น Group)
        $menus = Menu::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $navItems = [];

        foreach ($menus as $menu) {
            $navItems[] = NavigationItem::make($menu->name)
                ->label($menu->name)
                ->url($menu->route ? url($menu->route) : '#') // ตรวจสอบว่ามี route หรือไม่
                ->icon($menu->icon ?? 'heroicon-o-chevron-right')
                ->group($menu->group_name) // จัดกลุ่มตาม group_name ใน DB
                ->sort($menu->sort_order)
                ->visible(function () use ($menu) {
                    // ถ้ามีระบบ Permission สามารถเช็คได้ที่นี่
                    if ($menu->permission_name) {
                        return auth()->user()?->can($menu->permission_name);
                    }
                    return true;
                });
        }

        // รวมเมนูคงที่ (Static) เช่น Analytics เข้าไปด้วย
        // $navItems[] = NavigationItem::make('Analytics')
        //     ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
        //     ->icon('heroicon-o-presentation-chart-line')
        //     ->group('Reports')
        //     ->sort(100);

        return $navItems;
    }
}
