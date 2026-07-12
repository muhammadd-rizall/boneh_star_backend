<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'booking_id',
    'user_id',
    'proposed_price',
    'initial_price',
    'dp_amount',
    'note_to_customer',
    'status',
    'valid_until',
    'revision_note',
])]
class Quotation extends Model
{
    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $casts = [
        'proposed_price' => 'decimal:2',
        'initial_price' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'valid_until' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // The admin who created the quotation
    }
}
