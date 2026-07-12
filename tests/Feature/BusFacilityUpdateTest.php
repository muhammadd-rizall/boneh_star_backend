<?php

use App\Models\BusFacilities;
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

    $permission = Permission::create([
        'name' => 'Update Facility',
        'slug' => 'facilities.update',
        'group' => 'facilities',
    ]);

    $adminRole->permissions()->attach($permission->id);

    $this->admin = User::factory()->create();
    $this->admin->roles()->attach($adminRole->id);
});

test('admin can update a bus facility and unique validation ignores current facility', function () {
    $facility = BusFacilities::create([
        'name' => 'AC',
    ]);

    $response = $this->actingAs($this->admin)
        ->putJson("/api/admin/bus-facilities/{$facility->id}", [
            'name' => 'AC', // same name, should be ignored by unique validation
        ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Facility updated successfully',
    ]);

    $this->assertDatabaseHas('bus_facilities', [
        'id' => $facility->id,
        'name' => 'AC',
    ]);
});
