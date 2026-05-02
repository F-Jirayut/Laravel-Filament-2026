<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // แสดงลำดับการเรียง
                TextColumn::make('sort_order')
                    ->label('ลำดับ')
                    ->sortable(),

                // แสดงชื่อเมนู
                TextColumn::make('name')
                    ->label('ชื่อเมนู')
                    ->searchable()
                    ->sortable(),

                // แสดงไอคอน (ดึงชื่อมาจากฐานข้อมูลแล้วแสดงเป็นรูป)
                TextColumn::make('icon')
                    ->label('ไอคอน')
                    ->badge()
                    ->color('gray')
                    ->icon(fn (string $state): string => $state),

                // แสดงชื่อกลุ่ม
                TextColumn::make('group_name')
                    ->label('กลุ่มเมนู')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                // แสดง Route
                TextColumn::make('route')
                    ->label('เส้นทาง (Route)')
                    ->toggleable(isToggledHiddenByDefault: true),

                // เปิด-ปิดการใช้งาน (ใช้ ToggleColumn เพื่อให้กดเปลี่ยนสถานะได้จากหน้าตารางเลย)
                ToggleColumn::make('is_active')
                    ->label('สถานะ')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ตัวกรองแยกตามกลุ่ม
                // Tables\Filters\SelectFilter::make('group_name')
                //     ->label('กรองตามกลุ่ม')
                //     ->options([
                //         'System' => 'System',
                //         'Content' => 'Content',
                //         'Management' => 'Management',
                //     ]),

                // ตัวกรองสถานะ
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('สถานะการใช้งาน'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
