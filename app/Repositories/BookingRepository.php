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
        return $this->model->all();
    }

    public function getPaginatedBooking(int $perPage = 10)
    {
        return $this->model->latest()->paginate($perPage);
    }
}
