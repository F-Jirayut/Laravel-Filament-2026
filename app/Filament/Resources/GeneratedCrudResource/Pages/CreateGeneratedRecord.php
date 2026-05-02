<?php

namespace App\Filament\Resources\GeneratedCrudResource\Pages;

use App\Filament\Resources\GeneratedCrudResource;
use App\Filament\Resources\GeneratedCrudResource\Pages\Concerns\ResolvesGeneratedResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGeneratedRecord extends CreateRecord
{
    use ResolvesGeneratedResource;

    protected static string $resource = GeneratedCrudResource::class;
}
