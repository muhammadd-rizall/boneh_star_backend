<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService
{
    protected RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    // get all roles with permissions relation and count of users
    public function getAllRoles()
    {
        return $this->roleRepository->getAllWithPermissions();
    }

    // get role by id with permissions relation and count of users
    public function getRoleById(string $id)
    {
        return $this->roleRepository->findWithPermissions($id);
    }

    // create role
    public function createRole(array $data)
    {
        return $this->roleRepository->create($data);
    }

    // update role by id
    public function updateRole(string $id, array $data)
    {
        return $this->roleRepository->update($id, $data);
    }

    // delete role by id
    public function deleteRole(string $id)
    {
        return $this->roleRepository->delete($id);
    }

    // get all trashed roles with permissions relation and count of users
    public function getTrashedRoles()
    {
        return $this->roleRepository->allTrashed(['permissions']);
    }

    // restore role by id
    public function restoreRole(string $id)
    {
        return $this->roleRepository->restore($id);
    }

    // force delete role by id
    public function forceDeleteRole(string $id)
    {
        return $this->roleRepository->findTrashed($id)->forceDelete();
    }

    // sync permissions to role
    public function syncPermissions(string $roleId, array $permissionsId)
    {
        return $this->roleRepository->syncPermissions($roleId, $permissionsId);
    }

    // attach permissions to role
    public function attachPermissions(string $roleId, array $permissionsId)
    {
        return $this->roleRepository->attachPermissions($roleId, $permissionsId);
    }

    // detach permissions to role
    public function detachPermissions(string $roleId, array $permissionsId)
    {
        return $this->roleRepository->detachPermissions($roleId, $permissionsId);
    }

    // check if slug exists for create and update
    public function slugExists(string $slug, ?string $excludeId = null): bool
    {
        $role = $this->roleRepository->findBySlug($slug);
        if ($role) {
            if ($excludeId) {
                return $role->id !== $excludeId;
            }

            return true;
        }

        return false;
    }

    // get user statistics
    public function getStatistics(): array
    {
        return $this->roleRepository->getStatistics();
    }
}
