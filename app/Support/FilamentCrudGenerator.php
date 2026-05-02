<?php

namespace App\Support;

use App\Models\Menu;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class FilamentCrudGenerator
{
    public function generate(Menu $menu): array
    {
        $support = app(MenuCrudSupport::class);
        $permissionBase = $support->permissionBase($menu->permission_name);
        $names = $support->names($permissionBase, $menu->route);
        $columns = $support->normalizeColumns($menu->crud_columns);
        $created = [];

        $created = array_merge($created, $this->putFile(
            app_path("Models/{$names['model']}.php"),
            $this->modelTemplate($names, $columns)
        ));

        $created = array_merge($created, $this->putFile(
            database_path("factories/{$names['model']}Factory.php"),
            $this->factoryTemplate($names, $columns)
        ));

        if (! Schema::hasTable($names['table'])) {
            $created = array_merge($created, $this->putFile(
                database_path("migrations/{$this->migrationTimestamp()}_create_{$names['table']}_table.php"),
                $this->migrationTemplate($names, $columns)
            ));
        }

        $basePath = app_path("Filament/Resources/{$names['resourceFolder']}");

        $created = array_merge($created, $this->putFile(
            "{$basePath}/{$names['resourceClass']}.php",
            $this->resourceTemplate($names)
        ));

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');

        return [
            'created' => $created,
            'route' => "/admin/{$names['slug']}",
            'model' => $names['model'],
        ];
    }

    public function routeFor(string $permissionBase, ?string $route = null): string
    {
        return '/admin/' . app(MenuCrudSupport::class)->names($permissionBase, $route)['slug'];
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

    private function modelTemplate(array $names, array $columns): string
    {
        $fillable = implode("', '", app(MenuCrudSupport::class)->fillableColumns($columns));

        return <<<PHP
<?php

namespace App\Models;

use Database\Factories\\{$names['model']}Factory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['{$fillable}'])]
class {$names['model']} extends Model
{
    /** @use HasFactory<{$names['model']}Factory> */
    use HasFactory, SoftDeletes;

    protected static ?string \$recordTitleAttribute = 'name';
}
PHP;
    }

    private function factoryTemplate(array $names, array $columns): string
    {
        $properties = collect($columns)
            ->map(function (array $column): string {
                $fake = match ($column['type']) {
                    'text', 'rich_text' => "fake()->paragraph()",
                    'integer' => 'fake()->numberBetween(1, 1000)',
                    'boolean' => 'fake()->boolean()',
                    'date' => 'fake()->date()',
                    'datetime' => 'fake()->dateTime()',
                    default => 'fake()->sentence(3)',
                };

                return "            '{$column['name']}' => {$fake},";
            })
            ->implode("\n");

        return <<<PHP
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\\{$names['model']}>
 */
class {$names['model']}Factory extends Factory
{
    public function definition(): array
    {
        return [
{$properties}
        ];
    }
}
PHP;
    }

    private function migrationTemplate(array $names, array $columns): string
    {
        $columnLines = collect($columns)
            ->map(fn (array $column): string => '            ' . app(MenuCrudSupport::class)->migrationColumnLine($column))
            ->implode("\n");

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
        Schema::create('{$names['table']}', function (Blueprint \$table) {
            \$table->id();
{$columnLines}
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$names['table']}');
    }
};
PHP;
    }

    private function resourceTemplate(array $names): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$names['resourceFolder']};

use App\Filament\Resources\GeneratedCrudResource;
use App\Models\\{$names['model']};
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class {$names['resourceClass']} extends GeneratedCrudResource
{
    protected static ?string \$model = {$names['model']}::class;

    protected static ?string \$slug = '{$names['slug']}';

    protected static ?string \$menuPermissionBase = '{$names['base']}';

    protected static string|BackedEnum|null \$navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string \$recordTitleAttribute = 'name';
}
PHP;
    }
}
