<?php

namespace App\Support;

use App\Models\Menu;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use RuntimeException;

class MenuCrudSupport
{
    public function defaultColumns(): array
    {
        return [
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'string',
                'required' => true,
                'searchable' => true,
                'sortable' => true,
                'show_in_form' => true,
                'show_in_table' => true,
                'show_in_infolist' => true,
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'text',
                'required' => false,
                'searchable' => false,
                'sortable' => false,
                'show_in_form' => true,
                'show_in_table' => true,
                'show_in_infolist' => true,
            ],
        ];
    }

    public function normalizeColumns(?array $columns): array
    {
        $columns = filled($columns) ? $columns : $this->defaultColumns();

        return collect($columns)
            ->filter(fn (array $column): bool => filled($column['name'] ?? null))
            ->map(function (array $column): array {
                $name = Str::of($column['name'])
                    ->snake()
                    ->lower()
                    ->toString();

                $type = $column['type'] ?? 'string';

                return [
                    'name' => $name,
                    'label' => $column['label'] ?? Str::of($name)->replace('_', ' ')->title()->toString(),
                    'type' => in_array($type, $this->typeOptions(), true) ? $type : 'string',
                    'required' => (bool) ($column['required'] ?? false),
                    'searchable' => (bool) ($column['searchable'] ?? false),
                    'sortable' => (bool) ($column['sortable'] ?? false),
                    'show_in_form' => (bool) ($column['show_in_form'] ?? true),
                    'show_in_table' => (bool) ($column['show_in_table'] ?? true),
                    'show_in_infolist' => (bool) ($column['show_in_infolist'] ?? true),
                ];
            })
            ->values()
            ->all();
    }

    public function typeOptions(): array
    {
        return [
            'string',
            'text',
            'rich_text',
            'integer',
            'boolean',
            'date',
            'datetime',
        ];
    }

    public function permissionBase(string $permissionName): string
    {
        return preg_replace('/^(view|create|update|delete)_/', '', $permissionName);
    }

    public function names(string $permissionBase, ?string $route = null): array
    {
        $base = Str::of($permissionBase)
            ->replace(['-', '/'], ' ')
            ->snake()
            ->lower()
            ->toString();

        $slug = filled($route)
            ? Str::of($route)->after('/admin/')->trim('/')->toString()
            : Str::of(Str::plural($base))->replace('_', '-')->toString();

        $slugKey = Str::of($slug)->replace('-', '_')->snake()->toString();
        $model = Str::studly(Str::singular($slugKey));
        $pluralModel = Str::studly(Str::plural(Str::singular($slugKey)));

        return [
            'base' => $base,
            'slug' => $slug,
            'model' => $model,
            'pluralModel' => $pluralModel,
            'resourceFolder' => $pluralModel,
            'resourceClass' => "{$model}Resource",
            'table' => Str::snake($slugKey),
            'permissionView' => "view_{$base}",
            'permissionCreate' => "create_{$base}",
            'permissionUpdate' => "update_{$base}",
            'permissionDelete' => "delete_{$base}",
        ];
    }

    public function resourceClassFromRouteName(?string $routeName): string
    {
        if (blank($routeName)) {
            throw new RuntimeException('Unable to resolve generated resource without route name.');
        }

        $segments = explode('.', $routeName);
        $slug = $segments[count($segments) - 2] ?? null;

        if (blank($slug)) {
            throw new RuntimeException("Unable to resolve generated resource from route [{$routeName}].");
        }

        $names = $this->names(Str::singular(str_replace('-', '_', $slug)), "/admin/{$slug}");

        return "App\\Filament\\Resources\\{$names['resourceFolder']}\\{$names['resourceClass']}";
    }

    public function menuForPermissionBase(string $permissionBase): Menu
    {
        return Menu::query()
            ->where('permission_name', "view_{$permissionBase}")
            ->firstOrFail();
    }

    public function formComponents(Menu $menu): array
    {
        return collect($this->normalizeColumns($menu->crud_columns))
            ->filter(fn (array $column): bool => $column['show_in_form'])
            ->map(fn (array $column) => $this->makeFormComponent($column))
            ->values()
            ->all();
    }

    public function tableColumns(Menu $menu): array
    {
        // 1. ลบ ->all() ออกจากตรงนี้ เพื่อให้ $columns ยังคงเป็น Collection
        $columns = collect($this->normalizeColumns($menu->crud_columns))
            ->filter(fn (array $column): bool => $column['show_in_table'])
            ->map(fn (array $column) => $this->makeTableColumn($column))
            ->values(); // ไม่ต้องใส่ ->all()

        // 2. ตอนนี้ $columns เป็น Collection แล้ว จึงใช้ push() ได้
        $columns->push(
            TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime('d M Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
        );

        // 3. คืนค่าออกไปเป็น array ตามที่ return type กำหนดไว้
        return $columns->all();
    }

    public function infolistComponents(Menu $menu): array
    {
        $entries = collect($this->normalizeColumns($menu->crud_columns))
            ->filter(fn (array $column): bool => $column['show_in_infolist'])
            ->map(fn (array $column) => $this->makeInfolistEntry($column))
            ->values()
            ->all();

        $entries[] = TextEntry::make('created_at')
            ->label('Created At')
            ->dateTime('d M Y H:i');

        $entries[] = TextEntry::make('updated_at')
            ->label('Updated At')
            ->dateTime('d M Y H:i');

        return $entries;
    }

    public function fillableColumns(array $columns): array
    {
        return collect($this->normalizeColumns($columns))
            ->pluck('name')
            ->all();
    }

    public function migrationColumnLine(array $column): string
    {
        $nullable = $column['required'] ? '' : '->nullable()';

        return match ($column['type']) {
            'text', 'rich_text' => "\$table->text('{$column['name']}'){$nullable};",
            'integer' => "\$table->integer('{$column['name']}'){$nullable};",
            'boolean' => "\$table->boolean('{$column['name']}')->default(false);",
            'date' => "\$table->date('{$column['name']}'){$nullable};",
            'datetime' => "\$table->dateTime('{$column['name']}'){$nullable};",
            default => "\$table->string('{$column['name']}'){$nullable};",
        };
    }

    private function makeFormComponent(array $column): mixed
    {
        $component = match ($column['type']) {
            'text' => Textarea::make($column['name']),
            'rich_text' => RichEditor::make($column['name']),
            'integer' => TextInput::make($column['name'])->numeric(),
            'boolean' => Toggle::make($column['name']),
            'date' => DatePicker::make($column['name']),
            'datetime' => DateTimePicker::make($column['name']),
            default => TextInput::make($column['name']),
        };

        return $component
            ->label($column['label'])
            ->required($column['required'])
            ->columnSpanFull();
    }

    private function makeTableColumn(array $column): mixed
    {
        if ($column['type'] === 'boolean') {
            return IconColumn::make($column['name'])
                ->label($column['label'])
                ->boolean();
        }

        $textColumn = TextColumn::make($column['name'])
            ->label($column['label'])
            ->toggleable();

        if ($column['searchable']) {
            $textColumn->searchable();
        }

        if ($column['sortable']) {
            $textColumn->sortable();
        }

        return match ($column['type']) {
            'text' => $textColumn->limit(50)->wrap(),
            'rich_text' => $textColumn->html()->limit(50)->wrap(),
            'date' => $textColumn->date('d M Y'),
            'datetime' => $textColumn->dateTime('d M Y H:i'),
            default => $textColumn,
        };
    }

    private function makeInfolistEntry(array $column): mixed
    {
        if ($column['type'] === 'boolean') {
            return IconEntry::make($column['name'])
                ->label($column['label'])
                ->boolean();
        }

        $textEntry = TextEntry::make($column['name'])
            ->label($column['label'])
            ->placeholder('-');

        return match ($column['type']) {
            'rich_text' => $textEntry->html(),
            'date' => $textEntry->date('d M Y'),
            'datetime' => $textEntry->dateTime('d M Y H:i'),
            default => $textEntry,
        };
    }
}
