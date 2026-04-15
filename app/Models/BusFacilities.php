<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class BusFacilities extends Model
{
    /** @use HasFactory<\Database\Factories\BusFacilitiesFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the buses that have this facility.
     */
    public function buses(): BelongsToMany
    {
        return $this->belongsToMany(Buses::class, 'bus_facility', 'bus_facility_id', 'bus_id');
    }
}
