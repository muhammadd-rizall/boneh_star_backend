<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'booking_id',
    'bus_id',
    'driver_id',
    'seat_count',
    'assignment_notes',
])]
class BookingBus extends Model
{
    use HasFactory, HasUuids;

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Buses::class, 'bus_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
