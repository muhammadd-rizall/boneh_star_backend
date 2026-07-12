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
        return [
            'cards' => [
                'total_amount' => (float) $this->model->where('status', 'verified')->sum('amount'),
                'total_count' => $this->model->count(),
                'pending_count' => $this->model->where('status', 'pending')->count(),
                'verified_count' => $this->model->where('status', 'verified')->count(),
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

    /**
     * Get income report data.
     */
    public function getIncomeReportData(?int $month = null, ?int $year = null)
    {
        $query = $this->model->query()
            ->with(['transaction.booking.customer', 'paymentMethod'])
            ->where('status', 'verified');

        if ($month) {
            $query->whereMonth('payment_date', $month);
        }

        if ($year) {
            $query->whereYear('payment_date', $year);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    /**
     * Get monthly income statistics.
     */
    public function getIncomeStatistics(?int $year = null)
    {
        $year = $year ?? date('Y');

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', payment_date) as month"
            : "to_char(payment_date, 'YYYY-MM') as month";

        // Monthly income breakdown
        $monthlyIncome = $this->model->query()
            ->select(
                DB::raw($monthExpression),
                DB::raw('SUM(amount) as total_masuk'),
                DB::raw("SUM(CASE WHEN payment_type = 'dp' THEN amount ELSE 0 END) as total_dp"),
                DB::raw("SUM(CASE WHEN payment_type = 'settlement' THEN amount ELSE 0 END) as total_pelunasan")
            )
            ->where('status', 'verified')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Outstanding (unpaid transactions)
        $outstanding = DB::table('transactions as t')
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
            ->whereNotIn('b.status', ['cancelled'])
            ->orderBy('b.departure_date', 'asc')
            ->get();

        return [
            'monthly_chart' => $monthlyIncome,
            'outstanding_list' => $outstanding,
            'summary' => [
                'total_verified_income' => (float) $this->model->where('status', 'verified')->sum('amount'),
                'total_outstanding' => (float) $outstanding->sum('remaining_amount'),
            ],
        ];
    }
}
