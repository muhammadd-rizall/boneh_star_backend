<?php

namespace App\Services;

use App\Repositories\BusesRepository;

class BusAvailabilityReportService
{
    protected BusesRepository $busesRepository;

    public function __construct(BusesRepository $busesRepository)
    {
        $this->busesRepository = $busesRepository;
    }

    /**
     * Generate bus availability report.
     */
    public function generateReport(string $startDate, string $endDate): array
    {
        $buses = $this->busesRepository->getBusAvailability($startDate, $endDate);

        $reportData = $buses->map(function ($bus) {
            $schedules = $bus->bookingBuses->map(function ($bookingBus) {
                $booking = $bookingBus->booking;

                return [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'departure_date' => $booking->departure_date->format('Y-m-d H:i:s'),
                    'return_date' => $booking->return_date ? $booking->return_date->format('Y-m-d H:i:s') : null,
                    'status' => $booking->status,
                ];
            })->filter()->values();

            return [
                'id' => $bus->id,
                'name' => $bus->name,
                'plat_number' => $bus->plat_number,
                'status' => $bus->status,
                'schedules' => $schedules,
            ];
        });

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'items' => $reportData,
        ];
    }
}
