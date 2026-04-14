<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bus_id', 'image_path', 'is_primary'])]
class BusImage extends Model
{
    use HasFactory, HasUuids;

    public function casts()
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Get the bus that owns the image.
     */
    public function bus(): BelongsTo
    {
        return $this->belongsTo(Buses::class, 'bus_id');
    }
}
