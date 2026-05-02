<?php

namespace App\Filament\Resources\Libs;

use App\Filament\Resources\GeneratedCrudResource;
use App\Models\Lib;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class LibResource extends GeneratedCrudResource
{
    protected static ?string $model = Lib::class;

    protected static ?string $slug = 'lib';

    protected static ?string $menuPermissionBase = 'lib';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false;
    // }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()?->can('view_lib') ?? false;
    // }

    // public static function canCreate(): bool
    // {
    //     return auth()->user()?->can('create_lib') ?? false;
    // }

    // public static function canEdit($record): bool
    // {
    //     return auth()->user()?->can('update_lib') ?? false;
    // }

    // public static function canDelete($record): bool
    // {
    //     return auth()->user()?->can('delete_lib') ?? false;
    // }
}
