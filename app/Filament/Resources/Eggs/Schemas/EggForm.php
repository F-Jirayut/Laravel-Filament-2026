<?php

namespace App\Filament\Resources\Eggs\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EggForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            RichEditor::make('description')
                ->label('Description')
                ->nullable()
                ->columnSpanFull(),
        ])->columns(1);
    }
}