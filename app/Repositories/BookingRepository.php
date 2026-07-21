<?php

namespace App\Repositories;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingRepository extends BaseRepository
{
    public function __construct(Booking $booking)
    {
        $this->model = $booking;
    }

    public function getAllBooking()
    {
        return $this->model->with(['customer', 'activity', 'routeType', 'bookingBuses.bus', 'bookingBuses.driver'])->get();
    }

    public function getPaginatedBooking(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->with(['customer', 'activity', 'routeType', 'bookingBuses.bus', 'bookingBuses.driver']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getBookingById(string $id)
    {
        return $this->model->with(['customer', 'activity', 'routeType', 'bookingBuses.bus', 'bookingBuses.driver', 'quotation', 'transaction.payments'])->findOrFail($id);
    }

    public function getStatistics()
    {
        // Single query: GROUP BY status for all counts
        $statusCounts = $this->model->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Single query for passenger/bus aggregates on active statuses
        $activeStats = $this->model
            ->selectRaw('sum(passenger_count) as total_passengers, sum(bus_count_requested) as total_buses')
            ->whereIn('status', ['confirmed', 'ongoing', 'completed'])
            ->first();

        return [
            'total_bookings' => $statusCounts->sum(),
            'by_status' => [
                'pending'   => (int) ($statusCounts['pending']   ?? 0),
                'quoted'    => (int) ($statusCounts['quoted']    ?? 0),
                'confirmed' => (int) ($statusCounts['confirmed'] ?? 0),
                'ongoing'   => (int) ($statusCounts['ongoing']   ?? 0),
                'completed' => (int) ($statusCounts['completed'] ?? 0),
                'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
            ],
            'total_passengers'     => (int) ($activeStats->total_passengers  ?? 0),
            'total_buses_requested'=> (int) ($activeStats->total_buses       ?? 0),
        ];
    }

    /**
     * Get report data with filters.
     */
    public function getReportData(string $startDate, string $endDate, ?string $status = null, ?string $scope = null)
    {
        $query = $this->model->query()
            ->with(['customer', 'activity', 'routeType'])
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($scope) {
            $query->whereHas('routeType', function ($q) use ($scope) {
                $q->where('scope', $scope);
            });
        }

        return $query->orderBy('departure_date', 'desc')->get();
    }

    /**
     * Get report statistics for charts.
     */
    public function getReportStatistics(string $startDate, string $endDate, ?string $status = null, ?string $scope = null)
    {
        $baseQuery = $this->model->query()
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);

        if ($status) {
            $baseQuery->where('status', $status);
        }

        if ($scope) {
            $baseQuery->whereHas('routeType', function ($q) use ($scope) {
                $q->where('scope', $scope);
            });
        }

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at) as month"
            : "to_char(created_at, 'YYYY-MM') as month";

        // Monthly counts
        $monthlyStats = (clone $baseQuery)
            ->select(
                DB::raw($monthExpression),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Activity type counts
        $activityStats = (clone $baseQuery)
            ->select('activity_id', DB::raw('count(*) as count'))
            ->with('activity:id,name')
            ->groupBy('activity_id')
            ->get();

        // Status counts
        $statusStats = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Clone before the final aggregation to avoid executing on an already-consumed builder
        $summaryQuery = clone $baseQuery;

        return [
            'summary' => [
                'total_bookings'   => $summaryQuery->count(),
                'total_passengers' => $summaryQuery->sum('passenger_count'),
                'avg_passengers'   => round($summaryQuery->avg('passenger_count'), 1),
            ],
            'monthly_chart'  => $monthlyStats,
            'activity_chart' => $activityStats,
            'status_chart'   => $statusStats,
        ];
    }

    public function getCustomerBookings(string $customerId, int $perPage = 10)
    {
        return $this->model->where('customer_id', $customerId)
            ->with(['activity', 'routeType', 'transaction', 'quotation'])
            ->latest()
            ->paginate($perPage);
    }

    public function getCustomerBookingById(string $customerId, string $id)
    {
        return $this->model->where('customer_id', $customerId)
            ->with([
                'activity', 
                'routeType', 
                'bookingBuses.bus', 
                'bookingBuses.driver', 
                'quotation' => function ($query) {
                    $query->where('status', '!=', 'draft');
                }, 
                'transaction.payments', 
                'review'
            ])
            ->findOrFail($id);
    }
}
