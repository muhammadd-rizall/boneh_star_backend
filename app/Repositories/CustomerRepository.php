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

    public function getPaginatedCustomers(int $perPage = 10)
    {
        return $this->model->latest()->paginate($perPage);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->model->where('email', $email)->first();
    }

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
}
