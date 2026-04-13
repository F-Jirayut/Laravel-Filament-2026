<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Dashboard',
                'icon' => 'heroicon-o-home',
                'route' => '/admin',
                'permission_name' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Users',
                'icon' => 'heroicon-o-users',
                'route' => '/admin/users',
                'permission_name' => 'view user',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Roles',
                'icon' => 'heroicon-o-shield-check',
                'route' => '/admin/roles',
                'permission_name' => 'view role',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Permissions',
                'icon' => 'heroicon-o-lock-closed',
                'route' => '/admin/permissions',
                'permission_name' => 'view permission',
                'sort_order' => 4,
                'is_active' => true,
            ],
            // ⭐ เมนูใหม่จาก Permission Seeder
            [
                'name' => 'Posts',
                'icon' => 'heroicon-o-document-text',
                'route' => '/admin/posts',
                'permission_name' => 'view post',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name']],
                $menu
            );
        }
    }
}