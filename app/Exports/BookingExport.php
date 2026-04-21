<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bookings;

    public function __construct($bookings)
    {
        $this->bookings = $bookings;
    }

    public function collection()
    {
        return $this->bookings;
    }

    public function headings(): array
    {
        return [
            'Kode Booking',
            'Customer',
            'Asal',
            'Keberangkatan',
            'Kepulangan',
            'Jumlah Penumpang',
            'Jumlah Bus',
            'Status',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->booking_code,
            $booking->customer->name ?? '-',
            $booking->origin,
            $booking->departure_date->format('d/m/Y H:i'),
            $booking->return_date ? $booking->return_date->format('d/m/Y H:i') : '-',
            $booking->passenger_count,
            $booking->bus_count_requested,
            $booking->status,
        ];
    }
}
