<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use App\Support\MenuCrudSupport;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected array $permissionActions = [];
    protected string $oldPermissionName = '';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ดึงค่าเก่าจาก Hidden input และค่า Checkbox ล่าสุด
        $this->oldPermissionName = $data['old_permission_name'] ?? '';
        $this->permissionActions = $data['permission_actions'] ?? ['view'];
        $data['permission_name'] = $this->normalizePermissionName($data['permission_name']);
        $data['crud_columns'] = app(MenuCrudSupport::class)->normalizeColumns($data['crud_columns'] ?? []);

        unset($data['permission_actions'], $data['old_permission_name'], $data['generate_resource']);

        return $data;
    }

    protected function afterSave(): void
    {
        $newPermissionName = $this->record->permission_name;
        $newPermissionNamePregReplace = preg_replace('/^(view|create|update|delete)_/', '', $newPermissionName);
        // $oldPermissionNamePregReplace = preg_replace('/^(view|create|update|delete)_/', '', $this->oldPermissionName);
        $guardName = 'web';

        // 1. สร้างรายการ Permission ทั้งหมดที่เป็นไปได้ (Full Set) ของทั้งชื่อเก่าและชื่อใหม่
        // เพื่อหาว่าอันไหนควรอยู่ และอันไหนควรไป
        // $actions = ['view', 'create', 'update', 'delete'];

        // $oldFullSet = collect($actions)->map(fn ($action) => "{$action}_{$oldPermissionNamePregReplace}");

        // 2. Permission ที่ผู้ใช้ "เลือก" จริงๆ ในครั้งนี้
        $newPermissionsToKeep = collect($this->permissionActions)
            ->push('view')
            ->unique()
            ->map(fn ($action) => "{$action}_{$newPermissionNamePregReplace}");

        // 3. สร้างหรืออัปเดตสิทธิ์ที่ถูกติ๊ก
        foreach ($newPermissionsToKeep as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        $this->grantPermissionsToCurrentUserRoles($newPermissionsToKeep->all(), $guardName);

        // 4. การลบ: เราจะลบสิทธิ์ที่ "เคยเป็นของเมนูนี้" แต่ "ไม่ได้ถูกเลือกในครั้งนี้"
        // กรณีที่ 1: ถ้าเปลี่ยนชื่อเมนู -> สิทธิ์ชื่อเก่าทั้งหมดต้องถูกลบ
        // กรณีที่ 2: ถ้าชื่อเดิม -> สิทธิ์ที่เคยติ๊กไว้แต่ตอนนี้เอาออกต้องถูกลบ

        // $permissionsToDelete = $oldFullSet->diff($newPermissionsToKeep);
        // if ($permissionsToDelete->isNotEmpty()) {
        //     Permission::whereIn('name', $permissionsToDelete)
        //         ->where('guard_name', $guardName)
        //         ->delete();
        // }

        // ล้าง Cache ของ Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function normalizePermissionName(string $permissionName): string
    {
        $baseName = preg_replace('/^(view|create|update|delete)_/', '', $permissionName);
        $baseName = Str::of($baseName)->snake()->lower();

        return "view_{$baseName}";
    }

    private function grantPermissionsToCurrentUserRoles(array $permissions, string $guardName): void
    {
        $user = auth()->user();

        $user?->roles->each(fn ($role) => $role->givePermissionTo($permissions));

        Role::where('name', 'admin')
            ->where('guard_name', $guardName)
            ->first()
            ?->givePermissionTo($permissions);
    }
}
