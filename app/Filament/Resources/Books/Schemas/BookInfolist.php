<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Book Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Book Name'),

                        TextEntry::make('description')
                            ->label('Description')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
