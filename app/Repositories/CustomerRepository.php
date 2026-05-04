<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $customer)
    {
        $this->model = $customer;
    }

    public function getAllCustomers()
    {
        return $this->model->all();
    }

    // ==================================================
    // Admin
    // =================================================

    // table for admin view
    public function getPaginatedCustomers(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->newQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->model->where('email', $email)->first();
    }

    // statistif for admin view
    public function getStatistics(): array
    {
        return [
            'cards' => [
                'total' => $this->model->count(),
                'active' => $this->model->where('is_active', true)->count(),
                'new_members' => $this->model->where('created_at', '>=', now()->subDays(30))->count(),
                'loyal_customers' => $this->model->has('bookings', '>=', 3)->count(),
            ],
            'account_status' => [
                'active' => $this->model->where('is_active', true)->count(),
                'inactive' => $this->model->where('is_active', false)->count(),
            ],
        ];
    }

    // ==================================================
    // Customer
    // =================================================

    // statistic for customer view
    public function getCustomerBookingStatistics(string $customerId): array
    {
        $customer = $this->model->findOrFail($customerId);
        $bookings = $customer->bookings();

        return [
            'total_bookings' => $bookings->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
        ];
    }
}
