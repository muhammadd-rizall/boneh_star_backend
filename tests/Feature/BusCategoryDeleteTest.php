<?php

use App\Models\BusCategory;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $adminRole = Role::create([
        'name' => 'Super Admin',
        'slug' => 'super-admin',
        'is_active' => true,
    ]);

    // Attach all category permissions
    $permissions = [
        Permission::create(['name' => 'View Categories', 'slug' => 'categories.view', 'group' => 'categories']),
        Permission::create(['name' => 'Create Category', 'slug' => 'categories.create', 'group' => 'categories']),
        Permission::create(['name' => 'Update Category', 'slug' => 'categories.update', 'group' => 'categories']),
        Permission::create(['name' => 'Delete Category', 'slug' => 'categories.delete', 'group' => 'categories']),
        Permission::create(['name' => 'Restore Category', 'slug' => 'categories.restore', 'group' => 'categories']),
        Permission::create(['name' => 'Force Delete Category', 'slug' => 'categories.forceDelete', 'group' => 'categories']),
    ];

    foreach ($permissions as $permission) {
        $adminRole->permissions()->attach($permission->id);
    }

    $this->admin = User::factory()->create();
    $this->admin->roles()->attach($adminRole->id);
});

test('admin can soft delete, restore and force delete bus category', function () {
    $category = BusCategory::create([
        'name' => 'Big Bus',
        'min_capacity' => 40,
        'max_capacity' => 59,
        'description' => 'Large capacity bus',
    ]);

    // Test soft delete
    $response = $this->actingAs($this->admin)
        ->deleteJson("/api/admin/bus-categories/{$category->id}");

    $response->assertStatus(200);
    $this->assertSoftDeleted('bus_categories', [
        'id' => $category->id,
    ]);

    // Test restore
    $response = $this->actingAs($this->admin)
        ->postJson("/api/admin/bus-categories/{$category->id}/restore");

    $response->assertStatus(200);
    $this->assertDatabaseHas('bus_categories', [
        'id' => $category->id,
        'deleted_at' => null,
    ]);

    // Soft delete again for force delete test
    $category->delete();

    // Test force delete
    $response = $this->actingAs($this->admin)
        ->deleteJson("/api/admin/bus-categories/{$category->id}/force-delete");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('bus_categories', [
        'id' => $category->id,
    ]);
});
