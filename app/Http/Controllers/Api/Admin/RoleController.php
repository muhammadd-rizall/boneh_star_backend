<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\SyncPermissionRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Http\Resources\Admin\PermissionResource;
use App\Http\Resources\Admin\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Admin\RoleService;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }



    /// Display a listing of the resource.
    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $role = $this->roleService->getAllRoles();

        return response()->json([
            'success' => true,
            'data' => $role,
            'message' => 'Roles retrieved successfully',
        ], 200);
    }


    // Display the specified resource.
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role = $this->roleService->getRoleById($role->id);

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role),
            'message' => 'Role retrieved successfully',
        ], 200);
    }


    // Store a newly created resource in storage.
    public function store(StoreRoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $data = $request->validated();

        $permissionIds = $data['permissions_ids'] ?? [];
        unset($data['permissions_ids']);

        $role = $this->roleService->createRole($data);

        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role->load('permissions')),
            'message' => 'Role created successfully',
        ], 201);
    }



    // Update the specified resource in storage.
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $data = $request->validated();

        $permissionIds = $data['permissions_ids'] ?? [];
        unset($data['permissions_ids']);

        $updatedRole = $this->roleService->updateRole($role->id, $data);

        if ($permissionIds !== null) {
            $updatedRole = $this->roleService->syncPermissions($role->id, $permissionIds);
        }

        return response()->json([
            'success' => true,
            'data' => new RoleResource($updatedRole->load('permissions')),
            'message' => 'Role updated successfully',
        ], 200);
    }



    // Remove the specified resource from storage.
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $this->roleService->deleteRole($role->id);

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ], 200);
    }


    // Display a listing of the trashed resources.
    public function trashed()
    {
        $this->authorize('viewAny', Role::class);

        $roles = $this->roleService->getTrashedRoles();

        return response()->json([
            'success' => true,
            'data' => RoleResource::collection($roles),
            'message' => 'Trashed roles retrieved successfully',
        ], 200);
    }


    // Restore the specified resource from storage.
    public function restore($id)
    {
        $role = $this->roleService->getRoleById($id);

        $this->authorize('restore', $role);

        $this->roleService->restoreRole($id);

        return response()->json([
            'success' => true,
            'message' => 'Role restored successfully',
        ], 200);
    }


    // Permanently delete the specified resource from storage.
    public function forceDelete($id)
    {
        $role = $this->roleService->getRoleById($id);

        $this->authorize('forceDelete', $role);

        $this->roleService->forceDeleteRole($id);

        return response()->json([
            'success' => true,
            'message' => 'Role permanently deleted successfully',
        ], 200);
    }


    // Get all permissions grouped by group name
    public function permissions(Role $role)
    {
        $this->authorize('ViewAny', Role::class);

        $permissions = Permission::all()->groupBy('group');

        return response()->json([
            'success' => true,
            'data' => $permissions->map(function ($groupedPermissions) {
                return PermissionResource::collection($groupedPermissions);
            }),
            'message' => 'Permissions retrieved successfully',
        ], 200);
    }


    // Sync permissions of the role
    public function syncPermissions(SyncPermissionRequest $request, Role $role)
    {
        $this->authorize('managePermissions', $role);

        $permissionIds = $request->validated()['permissions_ids'];
        $role = $this->roleService->syncPermissions($role->id, $permissionIds);

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role),
            'message' => 'Role permissions synced successfully',
        ], 200);
    }


    // Attach a permission to the role
    public function attachPermission(Role $role, Permission $permission)
    {
        $this->authorize('managePermissions', $role);

        $this->roleService->syncPermissions($role->id, [$permission->id]);

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role),
            'message' => 'Permission attached to role successfully',
        ], 200);
    }



    // Detach a permission from the role
    public function detachPermission(Role $role, Permission $permission)
    {
        $this->authorize('managePermissions', $role);

        $role->$this->roleService->syncPermissions($role->id, [], [$permission->id]);

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role),
            'message' => 'Permission detached from role successfully',
        ], 200);
    }
}
