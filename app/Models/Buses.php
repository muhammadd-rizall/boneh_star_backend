<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bus_category_id', 'plat_number', 'name', 'seat_count', 'brand', 'status', 'notes'])]
class Buses extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'seat_count' => 'integer',
        ];
    }

    /**
     * Get the category that owns the bus.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BusCategory::class, 'bus_category_id');
    }

    /**
     * Get all images for the bus.
     */
    public function images(): HasMany
    {
        return $this->hasMany(BusImage::class, 'bus_id');
    }

    /**
     * Get the facilities for the bus.
     */
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(BusFacilities::class, 'bus_facility', 'bus_id', 'bus_facility_id');
    }

    /**
     * Get all booking assignments for the bus.
     */
    public function bookingBuses(): HasMany
    {
        return $this->hasMany(BookingBus::class, 'bus_id');
    }
}
