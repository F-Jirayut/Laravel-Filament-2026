<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Post Name')
                ->required()
                ->maxLength(255)
                ->placeholder('Enter post name')
                ->columnSpanFull(),

            // Textarea::make('description')
            //     ->label('Description')
            //     ->rows(4)
            //     ->nullable()
            //     ->placeholder('Enter post description')
            //     ->columnSpanFull(),

            // หากต้องการใช้ Rich Text Editor แทน Textarea ให้ใช้โค้ดด้านล่างแทน
            RichEditor::make('description')
                ->label('Description')
                ->nullable()
                ->columnSpanFull(),
        ])->columns(1);
    }
}
