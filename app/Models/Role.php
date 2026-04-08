<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('name', 'slug', 'is_active')]
class Role extends Model
{
    use HasFactory, HasUuids, SoftDeletes;


    public function casts()
    {
        return [
            'is_active' => 'boolean',
        ];
    }


    //get users of the role
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')->withTimestamps();
    }


    //get permissions of the role
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')->withTimestamps();
    }


    //check if the role has a specific permission
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }


    //scope to get only active roles
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
