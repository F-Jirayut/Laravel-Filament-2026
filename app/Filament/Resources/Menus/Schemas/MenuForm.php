<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Support\MenuCrudSupport;
use Spatie\Permission\Models\Permission;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('รายละเอียดเมนู')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->unique('menus', 'name', ignoreRecord: true),
                            TextInput::make('icon'),
                            TextInput::make('route'),
                            TextInput::make('group_name'),
                        ]),
                    ]),

                Section::make('ตั้งค่าสิทธิ์การเข้าถึง')
                    ->schema([

                        Hidden::make('old_permission_name')
                            ->afterStateHydrated(fn ($component, $record) => $component->state($record?->permission_name)),

                        TextInput::make('permission_name')
                            ->label('Permission Name')
                            ->formatStateUsing(fn ($state) => preg_replace('/^(view|create|update|delete)_/', '', $state ?? ''))
                            ->datalist(function () {
                                // ดึงชื่อ permission ทั้งหมดมา clean เอา prefix ออกเพื่อให้เหลือแต่ชื่อกลุ่ม
                                // เช่น 'view_users' -> 'users'
                                return Permission::pluck('name')
                                    ->map(fn ($name) => preg_replace('/^(view|create|update|delete)_/', '', $name))
                                    ->unique()
                                    ->filter()
                                    ->combine(
                                        Permission::pluck('name')
                                            ->map(fn ($name) => preg_replace('/^(view|create|update|delete)_/', '', $name))
                                            ->unique()
                                            ->filter()
                                    )
                                    ->toArray();
                            }) // ดึงชื่อที่มีอยู่มาเป็นตัวเลือกตอนพิมพ์
                            ->helperText('พิมพ์ชื่อสิทธิ์ใหม่ หรือเลือกจากรายการที่เคยมี')
                            ->required(),

                        CheckboxList::make('permission_actions')
                            ->label('CRUD Permissions')
                            ->options([
                                'view' => 'View (ดู)',
                                'create' => 'Create (สร้าง)',
                                'update' => 'Update (แก้ไข)',
                                'delete' => 'Delete (ลบ)',
                            ])
                            ->columns(4)
                            ->default(['view', 'create', 'update', 'delete'])
                            ->helperText('ระบบจะสร้าง permission ตาม action ที่เลือก เช่น view_book, create_book')
                            ->afterStateHydrated(function (CheckboxList $component, $record) {
                                if (! $record?->permission_name) {
                                    return;
                                }

                                $baseName = preg_replace('/^(view|create|update|delete)_/', '', $record->permission_name);
                                $actions = ['view', 'create', 'update', 'delete'];

                                $checkedActions = collect($actions)
                                    ->filter(fn (string $action): bool => Permission::where('name', "{$action}_{$baseName}")
                                        ->where('guard_name', 'web')
                                        ->exists())
                                    ->values()
                                    ->all();

                                $component->state($checkedActions ?: ['view']);
                            }),
                    ]),

                Section::make('อื่นๆ')
                    ->schema([
                        TextInput::make('sort_order')->numeric()->default(0),
                        Toggle::make('is_active')->default(true),
                        Toggle::make('generate_resource')
                            ->label('Generate CRUD Resource')
                            ->helperText('สร้าง Model, Migration และ Resource บาง ๆ ที่ใช้ runtime กลางจากข้อมูลในฐานข้อมูล')
                            ->default(true),
                    ]),

                Section::make('CRUD Columns')
                    ->schema([
                        Repeater::make('crud_columns')
                            ->label('Columns')
                            ->default(app(MenuCrudSupport::class)->defaultColumns())
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Column Name')
                                        ->required(),
                                    TextInput::make('label')
                                        ->label('Label')
                                        ->required(),
                                    Select::make('type')
                                        ->label('Type')
                                        ->options([
                                            'string' => 'String',
                                            'text' => 'Text',
                                            'rich_text' => 'Rich Text',
                                            'integer' => 'Integer',
                                            'boolean' => 'Boolean',
                                            'date' => 'Date',
                                            'datetime' => 'Date Time',
                                        ])
                                        ->default('string')
                                        ->required(),
                                    Toggle::make('required')
                                        ->default(false),
                                    Toggle::make('searchable')
                                        ->default(false),
                                    Toggle::make('sortable')
                                        ->default(false),
                                    Toggle::make('show_in_form')
                                        ->default(true),
                                    Toggle::make('show_in_table')
                                        ->default(true),
                                    Toggle::make('show_in_infolist')
                                        ->default(true),
                                ]),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['name'] ?? null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
