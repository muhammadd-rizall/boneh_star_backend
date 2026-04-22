<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    // get all data with roles relation
    public function getAllWithRole(array $relations = ['roles'])
    {
        return $this->model->with($relations)->get();
    }

    // find data by email
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->firstOrFail();
    }

    // get users by role id
    public function getByRole(string $roleId)
    {
        return $this->model->whereHas('roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId);
        })->with(['roles'])->get();
    }

    // get users with roles and search support
    public function getPaginatedUsers(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->with(['roles.permissions']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    // get user statistics
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'trashed' => $this->model->onlyTrashed()->count(),
        ];
    }
}
