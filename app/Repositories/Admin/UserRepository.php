<?php
namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
