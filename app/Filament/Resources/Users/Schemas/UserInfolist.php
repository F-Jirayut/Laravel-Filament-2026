<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ข้อมูลผู้ใช้งาน')
                    ->schema([
                        TextEntry::make('name')
                            ->label('ชื่อ'),

                        TextEntry::make('email')
                            ->label('อีเมล')
                            ->copyable(),

                        TextEntry::make('roles.name')
                            ->label('Roles')
                            ->badge()
                            ->separator(', ')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('สร้างเมื่อ')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
