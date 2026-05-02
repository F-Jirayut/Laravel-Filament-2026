<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description'])]
class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected static ?string $recordTitleAttribute = 'name';
}
