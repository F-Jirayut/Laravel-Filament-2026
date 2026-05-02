<?php

namespace App\Filament\Resources\GeneratedCrudResource\Pages;

use App\Filament\Resources\GeneratedCrudResource;
use App\Filament\Resources\GeneratedCrudResource\Pages\Concerns\ResolvesGeneratedResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGeneratedRecord extends EditRecord
{
    use ResolvesGeneratedResource;

    protected static string $resource = GeneratedCrudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
