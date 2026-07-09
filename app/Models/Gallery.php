<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'date', 'images', 'is_publish'])]
class Gallery extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'images' => 'array',
        'is_publish' => 'boolean',
        'date' => 'date',
    ];
}
