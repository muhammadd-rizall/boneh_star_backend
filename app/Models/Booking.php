<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'customer_id',
    'activity_id',
    'route_type_id',
    'booking_code',
    'departure_date',
    'return_date',
    'origin',
    'full_route',
    'passenger_count',
    'bus_count_requested',
    'special_notes',
    'status',
    'cancelled_by',
    'cancelled_reason',
])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory, HasUuids;

    protected $casts = [
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
        'passenger_count' => 'integer',
        'bus_count_requested' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class, 'activity_id');
    }

    public function routeType(): BelongsTo
    {
        return $this->belongsTo(RouteType::class, 'route_type_id');
    }

    public function bookingBuses(): HasMany
    {
        return $this->hasMany(BookingBus::class);
    }
}

