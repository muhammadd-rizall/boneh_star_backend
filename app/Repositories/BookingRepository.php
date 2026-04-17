<?php

namespace App\Repositories;

use App\Models\Booking;

class BookingRepository extends BaseRepository
{
    public function __construct(Booking $booking)
    {
        $this->model = $booking;
    }

    public function getAllBooking()
    {
        return $this->model->with(['customer', 'activity', 'routeType'])->get();
    }

    public function getPaginatedBooking(int $perPage = 10)
    {
        return $this->model->with(['customer', 'activity', 'routeType'])->latest()->paginate($perPage);
    }

    public function getBookingById(string $id)
    {
        return $this->model->with(['customer', 'activity', 'routeType'])->findOrFail($id);
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
}
