<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{

    public function __construct(User $user)
    {
        $this->model = $user;
    }


    //get all data with roles relation
    public function getAllWithRole(array $relations = ['roles'])
    {
        return $this->model->with($relations)->get();
    }


    //find data by email
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->firstOrFail();
    }


    //get users by role id
    public function getByRole(string $roleId)
    {
        return $this->model->whereHas('roles', function($query) use ($roleId) {
            $query->where('roles.id', $roleId);
        })->with(['roles'])->get();
    }
}
