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
        // Hanya ambil field yang diperlukan untuk dropdown/select, bukan semua kolom
        return $this->model->select('id', 'name', 'email', 'phone')->orderBy('name')->get();
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
        // Satu query aggregate untuk semua card statistics
        $agg = $this->model->selectRaw("
            count(*) as total,
            sum(CASE WHEN is_active = true  THEN 1 ELSE 0 END) as active,
            sum(CASE WHEN is_active = false THEN 1 ELSE 0 END) as inactive,
            sum(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_members
        ", [now()->subDays(30)])->first();

        // Query terpisah untuk loyal customers (perlu subquery has())
        $loyalCount = $this->model->has('bookings', '>=', 3)->count();

        return [
            'cards' => [
                'total'            => (int) ($agg->total       ?? 0),
                'active'           => (int) ($agg->active      ?? 0),
                'new_members'      => (int) ($agg->new_members ?? 0),
                'loyal_customers'  => $loyalCount,
            ],
            'account_status' => [
                'active'   => (int) ($agg->active   ?? 0),
                'inactive' => (int) ($agg->inactive ?? 0),
            ],
        ];
    }

    // ==================================================
    // Customer
    // =================================================

    // statistic for customer view
    public function getCustomerBookingStatistics(string $customerId): array
    {
        // Satu query GROUP BY menggantikan 5 query count terpisah
        $customer = $this->model->findOrFail($customerId);

        $stats = $customer->bookings()
            ->selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $total = $stats->sum();

        return [
            'total_bookings' => $total,
            'pending'        => (int) ($stats['pending']   ?? 0),
            'confirmed'      => (int) ($stats['confirmed'] ?? 0),
            'completed'      => (int) ($stats['completed'] ?? 0),
            'cancelled'      => (int) ($stats['cancelled'] ?? 0),
        ];
    }
}
