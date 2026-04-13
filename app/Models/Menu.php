<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('name', 'icon', 'route', 'permission_name', 'sort_order', 'is_active')]
class Menu extends Model
{
    use HasFactory;
}
