<?php

namespace App\Filament\Resources\GeneratedCrudResource\Pages;

use App\Filament\Resources\GeneratedCrudResource;
use App\Filament\Resources\GeneratedCrudResource\Pages\Concerns\ResolvesGeneratedResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGeneratedRecord extends ViewRecord
{
    use ResolvesGeneratedResource;

    protected static string $resource = GeneratedCrudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
