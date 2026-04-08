<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Services\Admin\UserService;

class UserController extends BaseController
{

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getAllUsers();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'message' => 'Users retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $roleIds = $data['role_ids'] ?? [];
        unset($data['role_ids']);

        $user = $this->userService->createUser($data);
        
        if (!empty($roleIds)) {
            $user->roles()->sync($roleIds);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('roles')),
            'message' => 'User created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user = $this->userService->getUserById($user->id);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'User retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        $roleIds = $data['role_ids'] ?? null;
        unset($data['role_ids']);

        $updatedUser = $this->userService->updateUser($user->id, $data);

        if ($roleIds !== null) {
            $updatedUser->roles()->sync($roleIds);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($updatedUser->load('roles')),
            'message' => 'User updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userService->deleteUser($user->id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }


    /**
     * Display a listing of the trashed resource.
     */

    public function trashed()
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getTrashedUsers();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'message' => 'Trashed users retrieved successfully',
        ], 200);
    }


    /**
     * Restore the specified resource from storage.
     */

    public function restore($id)
    {
        $user = $this->userService->restoreUser($id);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'User restored successfully',
        ], 200);
    }


    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $this->userService->forceDeleteUser($id);

        return response()->json([
            'success' => true,
            'message' => 'User permanently deleted successfully',
        ], 200);
    }
}
