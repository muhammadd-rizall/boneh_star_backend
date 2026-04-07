<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable('name', 'slug', 'group')]
class Permission extends Model
{


    //get roles of the permission
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')->withTimestamps();
    }


    //scope to get permissions by group
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
