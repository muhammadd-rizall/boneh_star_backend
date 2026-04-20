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
    public function generateReport(?int $month = null, ?int $year = null): array
    {
        $data = $this->paymentRepository->getIncomeReportData($month, $year);
        $statistics = $this->paymentRepository->getIncomeStatistics($year);

        return [
            'period' => [
                'month' => $month,
                'year' => $year ?? (int) date('Y'),
            ],
            'statistics' => $statistics,
            'items' => $data,
        ];
    }
}
