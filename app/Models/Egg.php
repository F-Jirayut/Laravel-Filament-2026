<?php

namespace App\Models;

use Database\Factories\EggFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description'])]
class Egg extends Model
{
    /** @use HasFactory<EggFactory> */
    use HasFactory, SoftDeletes;

    protected static ?string $recordTitleAttribute = 'name';
}