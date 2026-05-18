<?php

namespace App\Rules;

use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BeforeDepartureDate implements ValidationRule
{
    protected string $bookingId;

    public function __construct(string $bookingId)
    {
        $this->bookingId = $bookingId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $booking = Booking::find($this->bookingId);

        if (! $booking) {
            $fail('Data pemesanan tidak ditemukan.');

            return;
        }

        $paymentDate = strtotime($value);
        $departureDate = strtotime($booking->departure_date->format('Y-m-d'));

        if ($paymentDate >= $departureDate) {
            $fail('Tanggal pembayaran harus sebelum hari keberangkatan armada.');
        }
    }
}
