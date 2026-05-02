<?php

namespace App\Filament\Resources\Eggs;

use App\Filament\Resources\Eggs\Pages\CreateEgg;
use App\Filament\Resources\Eggs\Pages\EditEgg;
use App\Filament\Resources\Eggs\Pages\ListEgg;
use App\Filament\Resources\Eggs\Pages\ViewEgg;
use App\Filament\Resources\Eggs\Schemas\EggForm;
use App\Filament\Resources\Eggs\Schemas\EggInfolist;
use App\Filament\Resources\Eggs\Tables\EggsTable;
use App\Models\Egg;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EggResource extends Resource
{
    protected static ?string $model = Egg::class;

    protected static ?string $slug = 'eggs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EggForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EggInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EggsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEgg::route('/'),
            'create' => CreateEgg::route('/create'),
            'view' => ViewEgg::route('/{record}'),
            'edit' => EditEgg::route('/{record}/edit'),
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

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_egg') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_egg') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_egg') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_egg') ?? false;
    }
}