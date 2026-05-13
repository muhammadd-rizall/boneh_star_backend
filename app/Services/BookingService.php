<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected BookingRepository $bookingRepository;

    protected NotificationService $notificationService;

    public function __construct(
        BookingRepository $bookingRepository,
        NotificationService $notificationService
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * Get all bookings with eager loaded relations.
     */
    public function getAllBooking()
    {
        return $this->bookingRepository->getAllBooking();
    }

    /**
     * Get paginated bookings with eager loaded relations.
     */
    public function getPaginatedBooking(int $perPage = 10, ?string $search = null)
    {
        return $this->bookingRepository->getPaginatedBooking($perPage, $search);
    }

    /**
     * Get booking by ID with relations.
     */
    public function getBookingById(string $id)
    {
        return $this->bookingRepository->getBookingById($id);
    }

    /**
     * Create a new booking and assign buses/drivers.
     */
    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            $buses = $data['buses'] ?? [];
            unset($data['buses']);

            $booking = $this->bookingRepository->create($data);

            if (! empty($buses)) {
                foreach ($buses as $bus) {
                    $booking->bookingBuses()->create($bus);
                }
            }

            // Notify Admin
            $booking->load('customer');
            $customerName = $booking->customer->name ?? 'Pelanggan';
            $this->notificationService->notifyAdmin(
                'Permintaan Pemesanan Baru',
                "Pesanan baru dari {$customerName} telah diterima.",
                'booking',
                $booking
            );

            return $booking;
        });
    }

    /**
     * Update an existing booking and sync bus/driver assignments.
     */
    public function updateBooking(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $buses = $data['buses'] ?? [];
            $oldStatus = $this->bookingRepository->find($id)->status;
            unset($data['buses']);

            $booking = $this->bookingRepository->update($id, $data);

            if (isset($buses) && ! empty($buses)) {
                // Sync approach: remove existing and create new ones
                $booking->bookingBuses()->delete();
                foreach ($buses as $bus) {
                    $booking->bookingBuses()->create($bus);
                }
            }

            // Notify Customer if status changed
            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $statusText = match ($data['status']) {
                    'confirmed' => 'telah dikonfirmasi',
                    'ongoing' => 'sedang berlangsung',
                    'completed' => 'telah selesai',
                    'cancelled' => 'telah dibatalkan',
                    default => 'telah diperbarui',
                };

                $this->notificationService->notifyCustomer(
                    $booking->customer_id,
                    'Status Pemesanan Diperbarui',
                    "Status pemesanan Anda {$statusText}.",
                    'booking',
                    $booking
                );
            }

            return $booking;
        });
    }

    /**
     * Delete a booking by ID.
     */
    public function deleteBooking(string $id)
    {
        return $this->bookingRepository->delete($id);
    }

    /**
     * Get all trashed bookings.
     */
    public function getTrashedBooking()
    {
        return $this->bookingRepository->allTrashed(['customer', 'activity', 'routeType']);
    }

    /**
     * Restore a trashed booking by ID.
     */
    public function restoreBooking(string $id)
    {
        return $this->bookingRepository->restore($id);
    }

    /**
     * Permanently delete a trashed booking by ID.
     */
    public function forceDeleteBooking(string $id)
    {
        return $this->bookingRepository->forceDelete($id);
    }

    /**
     * Get booking statistics.
     */
    public function getBookingStatistics()
    {
        return $this->bookingRepository->getStatistics();
    }

    public function getCustomerBookings(string $customerId, int $perPage = 10)
    {
        return $this->bookingRepository->getCustomerBookings($customerId, $perPage);
    }

    public function getCustomerBookingById(string $customerId, string $id)
    {
        return $this->bookingRepository->getCustomerBookingById($customerId, $id);
    }
}
