<?php

namespace App\Services;

use App\Repositories\BookingRepository;

class BookingService
{
    protected BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    // get all bookings
    public function getAllBooking()
    {
        return $this->bookingRepository->getAllBooking();
    }

    // get paginated bookings
    public function getPaginatedBooking(int $perPage = 10)
    {
        return $this->bookingRepository->getPaginatedBooking($perPage);
    }

    // get booking by id
    public function getBookingById(string $id)
    {
        return $this->bookingRepository->getBookingById($id);
    }

    // create booking
    public function createBooking(array $data)
    {
        return $this->bookingRepository->create($data);
    }

    // update booking by id
    public function updateBooking(string $id, array $data)
    {
        return $this->bookingRepository->update($id, $data);
    }

    // delete booking by id
    public function deleteBooking(string $id)
    {
        return $this->bookingRepository->delete($id);
    }

    // get booking statistics
    public function getBookingStatistics()
    {
        return $this->bookingRepository->getStatistics();
    }
}
