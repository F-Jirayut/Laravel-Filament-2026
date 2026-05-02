<?php

namespace App\Models;

use Database\Factories\LibFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description'])]
class Lib extends Model
{
    protected $table = "lib";
    /** @use HasFactory<LibFactory> */
    use HasFactory, SoftDeletes;

    protected static ?string $recordTitleAttribute = 'name';
}
