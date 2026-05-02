<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Carbon\Carbon;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'id' => 1,
                'name' => 'Dashboard',
                'icon' => 'heroicon-o-home',
                'route' => '/admin',
                'group_name' => 'System',
                'permission_name' => 'view_dashboard',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Posts',
                'icon' => 'heroicon-o-document-text',
                'route' => '/admin/posts',
                'group_name' => 'Content',
                'permission_name' => 'view_post',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Book',
                'icon' => 'heroicon-o-book-open',
                'route' => '/admin/books',
                'group_name' => 'Content',
                'permission_name' => 'view_book',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Users',
                'icon' => 'heroicon-o-user',
                'route' => '/admin/users',
                'group_name' => 'Management',
                'permission_name' => 'view_user',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Roles',
                'icon' => 'heroicon-o-shield-check',
                'route' => '/admin/roles',
                'group_name' => 'Management',
                'permission_name' => 'view_role',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'name' => 'Menus',
                'icon' => 'heroicon-o-lock-closed',
                'route' => '/admin/menus',
                'group_name' => 'Management',
                'permission_name' => 'view_menu',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        /**
         * Parameter 1: ข้อมูลที่จะ insert
         * Parameter 2: คอลัมน์ที่ใช้เช็คว่าซ้ำหรือไม่ (Unique column)
         * Parameter 3: คอลัมน์ที่จะให้อัปเดตถ้าข้อมูลซ้ำ
         */
        Menu::upsert($menus, ['id'], [
            'name',
            'icon',
            'route',
            'group_name',
            'permission_name',
            'sort_order',
            'is_active',
            'updated_at'
        ]);
    }
}
