<?php

namespace App\Filament\Resources\Eggs\Pages;

use App\Filament\Resources\Eggs\EggResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEgg extends ViewRecord
{
    protected static string $resource = EggResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}