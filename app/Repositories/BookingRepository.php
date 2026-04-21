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
        return $this->model->with(['customer', 'activity', 'routeType', 'bookingBuses.bus', 'bookingBuses.driver'])->findOrFail($id);
    }

    public function getStatistics()
    {
        return [
            'total_bookings' => $this->model->count(),
            'by_status' => [
                'pending' => $this->model->where('status', 'pending')->count(),
                'quoted' => $this->model->where('status', 'quoted')->count(),
                'confirmed' => $this->model->where('status', 'confirmed')->count(),
                'ongoing' => $this->model->where('status', 'ongoing')->count(),
                'completed' => $this->model->where('status', 'completed')->count(),
                'cancelled' => $this->model->where('status', 'cancelled')->count(),
            ],
            'total_passengers' => $this->model->whereIn('status', ['confirmed', 'ongoing', 'completed'])->sum('passenger_count'),
            'total_buses_requested' => $this->model->whereIn('status', ['confirmed', 'ongoing', 'completed'])->sum('bus_count_requested'),
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

        // Monthly counts
        $monthlyStats = (clone $baseQuery)
            ->select(
                DB::raw("to_char(created_at, 'YYYY-MM') as month"),
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

        return [
            'summary' => [
                'total_bookings' => $baseQuery->count(),
                'total_passengers' => $baseQuery->sum('passenger_count'),
                'avg_passengers' => round($baseQuery->avg('passenger_count'), 1),
            ],
            'monthly_chart' => $monthlyStats,
            'activity_chart' => $activityStats,
            'status_chart' => $statusStats,
        ];
    }
}
