<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Book Name')
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
