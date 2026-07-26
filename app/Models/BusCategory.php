<?php

namespace App\Models;

use Database\Factories\BusCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'min_capacity', 'max_capacity', 'price_inside_min', 'price_inside_max', 'price_outside', 'description'])]
class BusCategory extends Model
{
    /** @use HasFactory<BusCategoryFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_capacity' => 'integer',
            'max_capacity' => 'integer',
            'price_inside_min' => 'integer',
            'price_inside_max' => 'integer',
            'price_outside' => 'integer',
        ];
    }
}
