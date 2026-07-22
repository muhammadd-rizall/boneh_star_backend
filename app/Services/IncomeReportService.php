<?php

namespace App\Services;

use App\Repositories\PaymentRepository;

class IncomeReportService
{
    protected PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Generate income report.
     */
    public function generateReport(
        ?int $month = null,
        ?int $year = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $status = null
    ): array {
        $data = $this->paymentRepository->getIncomeReportData($month, $year, $startDate, $endDate, $status);
        $statistics = $this->paymentRepository->getIncomeStatistics($year, $startDate, $endDate, $status);

        $resMonth = $month;
        $resYear = $year;
        if ($startDate) {
            $time = strtotime($startDate);
            $resMonth = (int) date('n', $time);
            $resYear = (int) date('Y', $time);
        }

        return [
            'period' => [
                'month' => $resMonth,
                'year' => $resYear ?? (int) date('Y'),
            ],
            'statistics' => $statistics,
            'items' => $data,
        ];
    }
}
