<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct( Role $role)
    {
        $this->model = $role;
    }


    //get all data with permissions relation and count of users
    public function getAllWithPermissions()
    {
        return $this->model->with('permissions')->withCount('users')->get();
    }


    //get data by id with permissions relation and count of users
    public function findWithPermissions(string $id)
    {
        return $this->model->with('permissions')->withCount('users')->findOrFail($id);
    }


    //sync permissions to role
    public function syncPermissions(string $roleid, array $permissionsids): Role
    {
        $role = $this->find($roleid);
        $role->permissions()->sync($permissionsids);
        return $role->load('permissions');
    }


    //attach permissions to role
    public function attachPermissions(string $roleid, array $permissionsids): Role
    {
        $role = $this->find($roleid);
        $role->permissions()->attach($permissionsids);
        return $role->load('permissions');
    }


    //detach permissions to role
    public function detachPermissions(string $roleid, array $permissionsids): Role
    {
        $role = $this->find($roleid);
        $role->permissions()->detach($permissionsids);
        return $role->load('permissions');
    }


    //find data by slug
    public function findBySlug(string $slug): ?Role
    {
        return $this->model->where('slug', $slug)->first();
    }

}
