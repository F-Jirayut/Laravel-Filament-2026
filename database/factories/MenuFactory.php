<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $menus = [
            [
                'name' => 'Dashboard',
                'icon' => 'heroicon-o-home',
                'route' => '/admin',
                'permission_name' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Users',
                'icon' => 'heroicon-o-users',
                'route' => '/admin/users',
                'permission_name' => 'view user',
                'sort_order' => 2,
            ],
            [
                'name' => 'Roles',
                'icon' => 'heroicon-o-shield-check',
                'route' => '/admin/roles',
                'permission_name' => 'view role',
                'sort_order' => 3,
            ],
            [
                'name' => 'Permissions',
                'icon' => 'heroicon-o-lock-closed',
                'route' => '/admin/permissions',
                'permission_name' => 'view permission',
                'sort_order' => 4,
            ],
        ];

        $menu = $this->faker->randomElement($menus);

        return [
            'name' => $menu['name'],
            'icon' => $menu['icon'],
            'route' => $menu['route'],
            'permission_name' => $menu['permission_name'],
            'sort_order' => $menu['sort_order'],
            'is_active' => true,
        ];
    }
}
