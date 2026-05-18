<?php

namespace App\Rules;

use App\Models\Booking;
use App\Models\BookingBus;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class BusAvailabilityRule implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $busId = $value;
        $departureDate = $this->data['departure_date'] ?? null;
        $returnDate = $this->data['return_date'] ?? null;
        $currentBookingId = $this->data['id'] ?? null;

        if (! $departureDate || ! $returnDate) {
            // Can't validate without dates, though other rules should catch this
            return;
        }

        // Check if the bus is already assigned to another booking that overlaps with these dates
        $isConflict = BookingBus::where('bus_id', $busId)
            ->whereHas('booking', function ($query) use ($departureDate, $returnDate, $currentBookingId) {
                $query->whereIn('status', ['pending', 'quoted', 'confirmed', 'ongoing'])
                    ->where(function ($q) use ($departureDate, $returnDate) {
                        $q->whereBetween('departure_date', [$departureDate, $returnDate])
                            ->orWhereBetween('return_date', [$departureDate, $returnDate])
                            ->orWhere(function ($inner) use ($departureDate, $returnDate) {
                                $inner->where('departure_date', '<=', $departureDate)
                                    ->where('return_date', '>=', $returnDate);
                            });
                    });

                if ($currentBookingId) {
                    $query->where('id', '!=', $currentBookingId);
                }
            })
            ->exists();

        if ($isConflict) {
            $fail('Unit bus yang dipilih tidak tersedia pada rentang tanggal tersebut karena sudah memiliki jadwal pemesanan lain.');
        }
    }
}
