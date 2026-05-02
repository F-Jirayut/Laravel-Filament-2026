<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FilamentCrudGenerator
{
    public function generate(string $name, string $permissionBase): array
    {
        $names = $this->names($name, $permissionBase);
        $created = [];

        $created = array_merge($created, $this->putFile(
            app_path("Models/{$names['model']}.php"),
            $this->modelTemplate($names)
        ));

        $created = array_merge($created, $this->putFile(
            database_path("factories/{$names['model']}Factory.php"),
            $this->factoryTemplate($names)
        ));

        if (! Schema::hasTable($names['table'])) {
            $created = array_merge($created, $this->putFile(
                database_path("migrations/{$this->migrationTimestamp()}_create_{$names['table']}_table.php"),
                $this->migrationTemplate($names)
            ));
        }

        $basePath = app_path("Filament/Resources/{$names['resourceFolder']}");

        $created = array_merge($created, $this->putFile(
            "{$basePath}/{$names['resourceClass']}.php",
            $this->resourceTemplate($names)
        ));

        $created = array_merge($created, $this->putFile(
            "{$basePath}/Schemas/{$names['model']}Form.php",
            $this->formTemplate($names)
        ));

        $created = array_merge($created, $this->putFile(
            "{$basePath}/Schemas/{$names['model']}Infolist.php",
            $this->infolistTemplate($names)
        ));

        $created = array_merge($created, $this->putFile(
            "{$basePath}/Tables/{$names['pluralModel']}Table.php",
            $this->tableTemplate($names)
        ));

        foreach (['List', 'Create', 'Edit', 'View'] as $page) {
            $created = array_merge($created, $this->putFile(
                "{$basePath}/Pages/{$page}{$names['model']}.php",
                $this->pageTemplate($names, $page)
            ));
        }

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');

        return [
            'created' => $created,
            'route' => "/admin/{$names['slug']}",
            'model' => $names['model'],
        ];
    }

    public function routeFor(string $name, string $permissionBase): string
    {
        return "/admin/{$this->names($name, $permissionBase)['slug']}";
    }

    private function names(string $name, string $permissionBase): array
    {
        $base = Str::of($permissionBase ?: $name)
            ->replace(['-', '/'], ' ')
            ->snake()
            ->lower()
            ->toString();

        $model = Str::studly(Str::singular($base));
        $pluralModel = Str::studly(Str::plural($model));

        return [
            'base' => $base,
            'model' => $model,
            'modelVariable' => Str::camel($model),
            'pluralModel' => $pluralModel,
            'resourceFolder' => $pluralModel,
            'resourceClass' => "{$model}Resource",
            'table' => Str::plural($base),
            'slug' => Str::of(Str::plural($base))->replace('_', '-')->toString(),
            'permissionView' => "view_{$base}",
            'permissionCreate' => "create_{$base}",
            'permissionUpdate' => "update_{$base}",
            'permissionDelete' => "delete_{$base}",
        ];
    }

    private function putFile(string $path, string $contents): array
    {
        if (File::exists($path)) {
            return [];
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);

        return [$path];
    }

    private function migrationTimestamp(): string
    {
        return now()->format('Y_m_d_His');
    }

    private function modelTemplate(array $n): string
    {
        return <<<PHP
<?php

namespace App\Models;

use Database\Factories\\{$n['model']}Factory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description'])]
class {$n['model']} extends Model
{
    /** @use HasFactory<{$n['model']}Factory> */
    use HasFactory, SoftDeletes;

    protected static ?string \$recordTitleAttribute = 'name';
}
PHP;
    }

    private function factoryTemplate(array $n): string
    {
        return <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\\{$n['model']}>
 */
class {$n['model']}Factory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
        ];
    }
}
PHP;
    }

    private function migrationTemplate(array $n): string
    {
        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$n['table']}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$n['table']}');
    }
};
PHP;
    }

    private function resourceTemplate(array $n): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$n['resourceFolder']};

use App\Filament\Resources\\{$n['resourceFolder']}\Pages\Create{$n['model']};
use App\Filament\Resources\\{$n['resourceFolder']}\Pages\Edit{$n['model']};
use App\Filament\Resources\\{$n['resourceFolder']}\Pages\List{$n['model']};
use App\Filament\Resources\\{$n['resourceFolder']}\Pages\View{$n['model']};
use App\Filament\Resources\\{$n['resourceFolder']}\Schemas\\{$n['model']}Form;
use App\Filament\Resources\\{$n['resourceFolder']}\Schemas\\{$n['model']}Infolist;
use App\Filament\Resources\\{$n['resourceFolder']}\Tables\\{$n['pluralModel']}Table;
use App\Models\\{$n['model']};
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class {$n['resourceClass']} extends Resource
{
    protected static ?string \$model = {$n['model']}::class;

    protected static ?string \$slug = '{$n['slug']}';

    protected static string|BackedEnum|null \$navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string \$recordTitleAttribute = 'name';

    public static function form(Schema \$schema): Schema
    {
        return {$n['model']}Form::configure(\$schema);
    }

    public static function infolist(Schema \$schema): Schema
    {
        return {$n['model']}Infolist::configure(\$schema);
    }

    public static function table(Table \$table): Table
    {
        return {$n['pluralModel']}Table::configure(\$table);
    }

    public static function getPages(): array
    {
        return [
            'index' => List{$n['model']}::route('/'),
            'create' => Create{$n['model']}::route('/create'),
            'view' => View{$n['model']}::route('/{record}'),
            'edit' => Edit{$n['model']}::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('{$n['permissionView']}') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('{$n['permissionCreate']}') ?? false;
    }

    public static function canEdit(\$record): bool
    {
        return auth()->user()?->can('{$n['permissionUpdate']}') ?? false;
    }

    public static function canDelete(\$record): bool
    {
        return auth()->user()?->can('{$n['permissionDelete']}') ?? false;
    }
}
PHP;
    }

    private function formTemplate(array $n): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$n['resourceFolder']}\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class {$n['model']}Form
{
    public static function configure(Schema \$schema): Schema
    {
        return \$schema->components([
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
PHP;
    }

    private function infolistTemplate(array $n): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$n['resourceFolder']}\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class {$n['model']}Infolist
{
    public static function configure(Schema \$schema): Schema
    {
        return \$schema
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
PHP;
    }

    private function tableTemplate(array $n): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$n['resourceFolder']}\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class {$n['pluralModel']}Table
{
    public static function configure(Table \$table): Table
    {
        return \$table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('Description')
                    ->html()
                    ->limit(50)
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
PHP;
    }

    private function pageTemplate(array $n, string $page): string
    {
        $body = match ($page) {
            'List' => "use Filament\\Actions\\CreateAction;\nuse Filament\\Resources\\Pages\\ListRecords;\n\nclass List{$n['model']} extends ListRecords\n{\n    protected static string \$resource = {$n['resourceClass']}::class;\n\n    protected function getHeaderActions(): array\n    {\n        return [\n            CreateAction::make(),\n        ];\n    }\n}",
            'Create' => "use Filament\\Resources\\Pages\\CreateRecord;\n\nclass Create{$n['model']} extends CreateRecord\n{\n    protected static string \$resource = {$n['resourceClass']}::class;\n}",
            'Edit' => "use Filament\\Actions\\DeleteAction;\nuse Filament\\Actions\\ForceDeleteAction;\nuse Filament\\Actions\\RestoreAction;\nuse Filament\\Actions\\ViewAction;\nuse Filament\\Resources\\Pages\\EditRecord;\n\nclass Edit{$n['model']} extends EditRecord\n{\n    protected static string \$resource = {$n['resourceClass']}::class;\n\n    protected function getHeaderActions(): array\n    {\n        return [\n            ViewAction::make(),\n            DeleteAction::make(),\n            ForceDeleteAction::make(),\n            RestoreAction::make(),\n        ];\n    }\n}",
            'View' => "use Filament\\Actions\\EditAction;\nuse Filament\\Resources\\Pages\\ViewRecord;\n\nclass View{$n['model']} extends ViewRecord\n{\n    protected static string \$resource = {$n['resourceClass']}::class;\n\n    protected function getHeaderActions(): array\n    {\n        return [\n            EditAction::make(),\n        ];\n    }\n}",
        };

        return <<<PHP
<?php

namespace App\Filament\Resources\\{$n['resourceFolder']}\Pages;

use App\Filament\Resources\\{$n['resourceFolder']}\\{$n['resourceClass']};
{$body}
PHP;
    }
}
