<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use App\Support\FilamentCrudGenerator;
use App\Support\MenuCrudSupport;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected array $permissionActions = [];
    protected bool $shouldGenerateResource = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionActions = $data['permission_actions'] ?? ['view'];
        $this->shouldGenerateResource = (bool) ($data['generate_resource'] ?? false);
        $data['permission_name'] = $this->normalizePermissionName($data['permission_name']);
        $data['crud_columns'] = app(MenuCrudSupport::class)->normalizeColumns($data['crud_columns'] ?? []);

        if (blank($data['route'] ?? null) && $this->shouldGenerateResource) {
            $data['route'] = app(FilamentCrudGenerator::class)->routeFor(
                $this->permissionBase($data['permission_name']),
                $data['route'] ?? null
            );
        }

        unset($data['permission_actions'], $data['old_permission_name'], $data['generate_resource']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $actions = $this->permissionActions ?: ['view'];
        $baseName = preg_replace('/^(view|create|update|delete)_/', '', $this->record?->permission_name);
        $guardName = 'web';

        $newPermissionsToKeep = collect($actions)
            ->push('view')
            ->unique()
            ->map(fn ($action) => "{$action}_{$baseName}");

        foreach ($newPermissionsToKeep as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        $this->grantPermissionsToCurrentUserRoles($newPermissionsToKeep->all(), $guardName);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if ($this->shouldGenerateResource) {
            app(FilamentCrudGenerator::class)->generate($this->record);
        }
    }

    private function normalizePermissionName(string $permissionName): string
    {
        $baseName = preg_replace('/^(view|create|update|delete)_/', '', $permissionName);
        $baseName = Str::of($baseName)->snake()->lower();

        return "view_{$baseName}";
    }

    private function permissionBase(string $permissionName): string
    {
        return preg_replace('/^(view|create|update|delete)_/', '', $permissionName);
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
