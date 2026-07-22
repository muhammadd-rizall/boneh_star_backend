<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    public function getPaginatedPayments(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->with(['transaction', 'paymentMethod', 'verifiedBy']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('transaction', function ($tq) use ($search) {
                    $tq->where('transaction_code', 'like', "%{$search}%");
                })
                    ->orWhereHas('paymentMethod', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getTrashedPayments()
    {
        return $this->model->onlyTrashed()->with(['transaction', 'paymentMethod', 'verifiedBy'])->latest()->get();
    }

    /**
     * Get statistics for payments.
     */
    public function getStatistics(): array
    {
        // One query for all counts/amounts
        $agg = $this->model->selectRaw("
            count(*) as total_count,
            sum(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) as pending_count,
            sum(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified_count,
            sum(CASE WHEN status = 'verified' THEN amount ELSE 0 END) as total_amount
        ")->first();

        return [
            'cards' => [
                'total_amount'   => (float) ($agg->total_amount   ?? 0),
                'total_count'    => (int)   ($agg->total_count    ?? 0),
                'pending_count'  => (int)   ($agg->pending_count  ?? 0),
                'verified_count' => (int)   ($agg->verified_count ?? 0),
            ],
            'by_status' => $this->model->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray(),
            'by_type' => $this->model->select('payment_type', DB::raw('count(*) as count'))
                ->groupBy('payment_type')
                ->get()
                ->pluck('count', 'payment_type')
                ->toArray(),
        ];
    }

    public function getIncomeReportData(
        ?int $month = null,
        ?int $year = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $status = null
    ) {
        $query = $this->model->query()
            ->with(['transaction.booking.customer', 'paymentMethod'])
            ->where('status', 'verified');

        if ($status === 'fully_paid') {
            $query->where('payment_type', 'settlement');
        } elseif ($status === 'dp') {
            $query->where('payment_type', 'dp');
        } elseif ($status === 'unpaid') {
            $query->whereRaw('1 = 0');
        }

        if ($startDate) {
            $query->whereDate('payment_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('payment_date', '<=', $endDate);
        }

        if (!$startDate && !$endDate) {
            if ($month) {
                $query->whereMonth('payment_date', $month);
            }

            if ($year) {
                $query->whereYear('payment_date', $year);
            }
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function getIncomeStatistics(
        ?int $year = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $status = null
    ) {
        if ($startDate) {
            $year = (int) date('Y', strtotime($startDate));
        } else {
            $year = $year ?? (int) date('Y');
        }

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', payment_date) as month"
            : "to_char(payment_date, 'YYYY-MM') as month";

        // Monthly income breakdown
        $monthlyIncomeQuery = $this->model->query()
            ->select(
                DB::raw($monthExpression),
                DB::raw('SUM(amount) as total_masuk'),
                DB::raw("SUM(CASE WHEN payment_type = 'dp' THEN amount ELSE 0 END) as total_dp"),
                DB::raw("SUM(CASE WHEN payment_type = 'settlement' THEN amount ELSE 0 END) as total_pelunasan")
            )
            ->where('status', 'verified');

        if ($status === 'fully_paid') {
            $monthlyIncomeQuery->where('payment_type', 'settlement');
        } elseif ($status === 'dp') {
            $monthlyIncomeQuery->where('payment_type', 'dp');
        } elseif ($status === 'unpaid') {
            $monthlyIncomeQuery->whereRaw('1 = 0');
        }

        if ($startDate) {
            $monthlyIncomeQuery->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $monthlyIncomeQuery->whereDate('payment_date', '<=', $endDate);
        }
        if (!$startDate && !$endDate) {
            $monthlyIncomeQuery->whereYear('payment_date', $year);
        }

        $monthlyIncome = $monthlyIncomeQuery->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Outstanding (unpaid transactions)
        $outstandingQuery = DB::table('transactions as t')
            ->join('bookings as b', 'b.id', '=', 't.booking_id')
            ->join('customers as c', 'c.id', '=', 'b.customer_id')
            ->select(
                't.invoice_number',
                't.remaining_amount',
                'c.name as penyewa',
                'b.departure_date'
            )
            ->where('t.remaining_amount', '>', 0)
            ->whereNull('t.deleted_at')
            ->whereNotIn('b.status', ['cancelled']);

        if ($status === 'fully_paid' || $status === 'dp') {
            $outstandingQuery->whereRaw('1 = 0');
        }

        if ($startDate) {
            $outstandingQuery->whereDate('b.departure_date', '>=', $startDate);
        }
        if ($endDate) {
            $outstandingQuery->whereDate('b.departure_date', '<=', $endDate);
        }
        if (!$startDate && !$endDate) {
            if ($year) {
                $outstandingQuery->whereYear('b.departure_date', $year);
            }
        }

        $outstanding = $outstandingQuery->orderBy('b.departure_date', 'asc')->get();

        // Calculate verified income sum based on active filters
        $totalIncomeQuery = $this->model->where('status', 'verified');
        
        if ($status === 'fully_paid') {
            $totalIncomeQuery->where('payment_type', 'settlement');
        } elseif ($status === 'dp') {
            $totalIncomeQuery->where('payment_type', 'dp');
        } elseif ($status === 'unpaid') {
            $totalIncomeQuery->whereRaw('1 = 0');
        }

        if ($startDate) {
            $totalIncomeQuery->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $totalIncomeQuery->whereDate('payment_date', '<=', $endDate);
        }
        if (!$startDate && !$endDate && $year) {
            $totalIncomeQuery->whereYear('payment_date', $year);
        }

        return [
            'monthly_chart' => $monthlyIncome,
            'outstanding_list' => $outstanding,
            'summary' => [
                'total_verified_income' => (float) $totalIncomeQuery->sum('amount'),
                'total_outstanding' => (float) $outstanding->sum('remaining_amount'),
            ],
        ];
    }
}
