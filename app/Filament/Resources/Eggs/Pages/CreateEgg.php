<?php

namespace App\Filament\Resources\Eggs\Pages;

use App\Filament\Resources\Eggs\EggResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEgg extends CreateRecord
{
    protected static string $resource = EggResource::class;
}