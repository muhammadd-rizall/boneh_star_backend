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

        // Gunakan format Y-m-d murni untuk menghindari efek timezone
        $paymentDate   = $value; // sudah string 'Y-m-d' dari input
        $departureDate = $booking->departure_date->format('Y-m-d');
        $today         = now()->format('Y-m-d');

        // 1. Check if declared payment date is before departure date
        if ($paymentDate >= $departureDate) {
            $fail('Tanggal pembayaran harus sebelum hari keberangkatan armada.');
            return;
        }

        // 2. Check if today's date is before departure date
        if ($today >= $departureDate) {
            $fail('Batas waktu pembayaran telah habis (harus sebelum hari keberangkatan armada).');
            return;
        }
    }
}
