<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use HasFactory, HasUuids, SoftDeletes;

    protected $casts = [
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
        'passenger_count' => 'integer',
        'bus_count_requested' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = static::generateBookingCode();
            }
        });
    }

    public static function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'BK-'.$date.'-';

        $lastBooking = static::withTrashed()
            ->where('booking_code', 'like', $prefix.'%')
            ->orderBy('booking_code', 'desc')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix.$nextNumber;
    }

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

    public function quotation(): HasOne
    {
        return $this->hasOne(Quotation::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}
