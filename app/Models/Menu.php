<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('name', 'icon', 'route', 'group_name', 'permission_name', 'crud_columns', 'sort_order', 'is_active',)]
class Menu extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'crud_columns' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
