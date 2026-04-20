<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'target_type',
    'target_id',
    'title',
    'message',
    'type',
    'reference_type',
    'reference_id',
    'is_read',
    'read_at',
])]
class Notification extends Model
{
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the target (User or Customer) of the notification.
     */
    public function target(): MorphTo
    {
        // This is a manual morph relation since we use 'admin'/'customer' instead of class names in target_type
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_id');
    }

    /**
     * Get the reference model (e.g., Booking, Payment).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
