<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

#[Fillable('name', 'icon', 'route', 'group_name', 'permission_name', 'sort_order', 'is_active',)]
class Menu extends Model
{
    use HasFactory;

    protected static function booted()
    {
        // static::saved(function ($menu) {
        //     // ดึงค่าจาก request (CheckboxList ที่เราตั้งชื่อว่า permission_actions)
        //     $actions = request()->input('components.0.components.1.components.1.state')
        //             ?? ['view', 'create', 'update', 'delete'];

        //     // หมายเหตุ: วิธีที่ชัวร์กว่าคือการใช้ $menu->permission_name
        //     if ($menu->permission_name) {
        //         foreach ($actions as $action) {
        //             $permissionName = $action . '_' . $menu->permission_name;

        //             Permission::firstOrCreate([
        //                 'name' => $permissionName,
        //                 'guard_name' => 'web',
        //             ]);
        //         }
        //     }
        // });
    }
}
