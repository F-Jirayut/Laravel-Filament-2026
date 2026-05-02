<?php

namespace App\Filament\Resources\GeneratedCrudResource\Pages;

use App\Filament\Resources\GeneratedCrudResource;
use App\Filament\Resources\GeneratedCrudResource\Pages\Concerns\ResolvesGeneratedResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListGeneratedRecords extends ListRecords
{
    use ResolvesGeneratedResource;

    protected static string $resource = GeneratedCrudResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
