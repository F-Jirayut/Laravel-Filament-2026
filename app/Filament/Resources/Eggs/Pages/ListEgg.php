<?php

namespace App\Filament\Resources\Eggs\Pages;

use App\Filament\Resources\Eggs\EggResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEgg extends ListRecords
{
    protected static string $resource = EggResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}