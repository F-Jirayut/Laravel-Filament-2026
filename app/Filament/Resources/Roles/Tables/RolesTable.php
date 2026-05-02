<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('ชื่อบทบาท (Role Name)')
                    ->searchable()
                    ->sortable()
                    ->copyable() // ให้กด Copy ชื่อไปใช้งานต่อได้ง่ายๆ
                    ->weight('bold'),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge() // เปลี่ยนให้เป็น Badge สวยๆ
                    ->color('info')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i') // รูปแบบวันที่แบบไทย
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // ซ่อนไว้ก่อน ให้ User เลือกเปิดเองได้
            ])
            ->filters([
                // สามารถเพิ่ม Filter ตาม guard_name ได้ที่นี่
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
