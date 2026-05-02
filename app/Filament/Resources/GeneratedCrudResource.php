<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneratedCrudResource\Pages\CreateGeneratedRecord;
use App\Filament\Resources\GeneratedCrudResource\Pages\EditGeneratedRecord;
use App\Filament\Resources\GeneratedCrudResource\Pages\ListGeneratedRecords;
use App\Filament\Resources\GeneratedCrudResource\Pages\ViewGeneratedRecord;
use App\Models\Menu;
use App\Support\MenuCrudSupport;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

abstract class GeneratedCrudResource extends Resource
{
    protected static ?string $menuPermissionBase = null;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getMenu()->name)
                    ->schema(app(MenuCrudSupport::class)->formComponents(static::getMenu()))
                    ->columns(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getMenu()->name)
                    ->schema(app(MenuCrudSupport::class)->infolistComponents(static::getMenu()))
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(app(MenuCrudSupport::class)->tableColumns(static::getMenu()))
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeneratedRecords::route('/'),
            'create' => CreateGeneratedRecord::route('/create'),
            'view' => ViewGeneratedRecord::route('/{record}'),
            'edit' => EditGeneratedRecord::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return static::getMenu()->name;
    }

    public static function getModelLabel(): string
    {
        return str(static::getMenu()->name)->singular()->toString();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getMenu()->name;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_' . static::getMenuPermissionBase()) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_' . static::getMenuPermissionBase()) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_' . static::getMenuPermissionBase()) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_' . static::getMenuPermissionBase()) ?? false;
    }

    protected static function getMenu(): Menu
    {
        return app(MenuCrudSupport::class)->menuForPermissionBase(static::getMenuPermissionBase());
    }

    protected static function getMenuPermissionBase(): string
    {
        return static::$menuPermissionBase
            ?? throw new \RuntimeException('Missing $menuPermissionBase on generated resource.');
    }
}
