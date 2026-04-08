<?php

namespace App\Services\Admin;

use App\Repositories\Admin\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    //get all users with role and permissions relation
    public function getAllUsers()
    {
        return $this->userRepository->getAllWithRole(['roles.permissions']);
    }



    //get user by id with role and permissions relation
    public function getUserById($id)
    {
        return $this->userRepository->find($id, ['roles.permissions']);
    }



    //create user with hashed password
    public function createUser(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->create($data);
    }



    //update user with hashed password if password is provided
    public function updateUser(string $id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }else {
            unset($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }



    //delete user by id
    public function deleteUser(string $id)
    {
        return $this->userRepository->delete($id);
    }



    //get all trashed users with role and permissions relation
    public function getTrashedUsers()
    {
        return $this->userRepository->allTrashed(['roles.permissions']);
    }



    //restore user by id
    public function restoreUser(string $id)
    {
        $user = $this->userRepository->findTrashed($id);
        return $user->restore();
    }



    //force delete user by id
    public function forceDeleteUser(string $id)
    {
        $user = $this->userRepository->findTrashed($id);
        return $user->forceDelete();
    }



    //check if email already exists except for the given user id
    public function emailExists(string $email, ?string $exceptId = null): bool
    {
        $user = $this->userRepository->findByEmail($email);

        return $user && $user->id !== $exceptId;
    }



    //get users by role id
    public function getUsersByRole(string $roleId)
    {
        return $this->userRepository->getByRole($roleId);
    }
}
