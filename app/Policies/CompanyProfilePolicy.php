<?php

namespace App\Policies;

use App\Models\CompanyProfile;
use App\Models\User;

class CompanyProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(config('company_profile.view'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CompanyProfile $companyProfile): bool
    {
        return $user->hasPermissionTo(config('company_profile.view'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(config('company_profile.update'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CompanyProfile $companyProfile): bool
    {
        return $user->hasPermissionTo(config('company_profile.update'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CompanyProfile $companyProfile): bool
    {
        return $user->hasPermissionTo(config('company_profile.delete'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CompanyProfile $companyProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CompanyProfile $companyProfile): bool
    {
        return false;
    }
}
