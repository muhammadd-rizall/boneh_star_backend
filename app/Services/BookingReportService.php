<?php

namespace App\Services;

use App\Repositories\BookingRepository;

class BookingReportService
{
    protected BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Generate booking report.
     */
    public function generateReport(string $startDate, string $endDate, ?string $status = null, ?string $scope = null): array
    {
        $data = $this->bookingRepository->getReportData($startDate, $endDate, $status, $scope);
        $statistics = $this->bookingRepository->getReportStatistics($startDate, $endDate, $status, $scope);

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'filters' => [
                'status' => $status,
                'scope' => $scope,
            ],
            'statistics' => $statistics,
            'items' => $data,
        ];
    }
}
