<?php

namespace App\Filament\Resources\GeneratedCrudResource\Pages\Concerns;

use App\Filament\Resources\GeneratedCrudResource;
use App\Support\MenuCrudSupport;

trait ResolvesGeneratedResource
{
    public static function getResource(): string
    {
        $routeName = request()->route()?->getName();

        if (filled($routeName)) {
            return app(MenuCrudSupport::class)->resourceClassFromRouteName($routeName);
        }

        return GeneratedCrudResource::class;
    }
}
