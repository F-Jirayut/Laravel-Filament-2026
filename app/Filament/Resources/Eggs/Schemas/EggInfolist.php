<?php

namespace App\Filament\Resources\Eggs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EggInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),

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