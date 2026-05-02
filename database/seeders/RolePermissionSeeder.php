<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // รีเซ็ต cache ของ Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // สร้าง Permissions ให้ครบตามเมนู
        $permissions = [
            'view_dashboard',
            'view_post',
            'create_post',
            'update_post',
            'delete_post',
            'view_book',
            'create_book',
            'update_book',
            'delete_book',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'view_menu',
            'create_menu',
            'update_menu',
            'delete_menu',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // สร้าง Roles
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        // กำหนด Permission ทั้งหมดให้ Admin
        $adminRole->syncPermissions(Permission::all());

        // // ค้นหา User ID = 3
        $user = User::find(3);

        // หากไม่มี ให้ใช้ User คนแรก
        if (!$user) {
            $user = User::first();
        }

        // หากยังไม่มี ให้สร้าง Admin ใหม่
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // กำหนด Role Admin
        $user->syncRoles([$adminRole]);
    }
}
