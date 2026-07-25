<?php

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer cannot access admin dashboard or notifications', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'sanctum')
        ->getJson('/api/admin/dashboard')
        ->assertStatus(403);

    $this->actingAs($customer, 'sanctum')
        ->getJson('/api/admin/notifications')
        ->assertStatus(403);
});

test('user without role cannot access admin dashboard or notifications', function () {
    $userWithoutRole = User::factory()->create(['is_active' => true]);

    $this->actingAs($userWithoutRole, 'sanctum')
        ->getJson('/api/admin/dashboard')
        ->assertStatus(403);

    $this->actingAs($userWithoutRole, 'sanctum')
        ->getJson('/api/admin/notifications')
        ->assertStatus(403);
});

test('inactive user cannot access admin dashboard or notifications', function () {
    $role = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
        'is_active' => true,
    ]);

    $inactiveUser = User::factory()->create(['is_active' => false]);
    $inactiveUser->roles()->attach($role->id);

    $this->actingAs($inactiveUser, 'sanctum')
        ->getJson('/api/admin/dashboard')
        ->assertStatus(403);

    $this->actingAs($inactiveUser, 'sanctum')
        ->getJson('/api/admin/notifications')
        ->assertStatus(403);
});

test('active admin user with role can access dashboard and notifications', function () {
    $superAdminRole = Role::create([
        'name' => 'Super Admin',
        'slug' => 'super-admin',
        'is_active' => true,
    ]);

    $adminUser = User::factory()->create(['is_active' => true]);
    $adminUser->roles()->attach($superAdminRole->id);

    $this->actingAs($adminUser, 'sanctum')
        ->getJson('/api/admin/dashboard')
        ->assertStatus(200);

    $this->actingAs($adminUser, 'sanctum')
        ->getJson('/api/admin/notifications')
        ->assertStatus(200);
});

test('active user with non-admin role cannot access admin dashboard or notifications', function () {
    $driverRole = Role::create([
        'name' => 'Driver',
        'slug' => 'driver',
        'is_active' => true,
    ]);

    $driverUser = User::factory()->create(['is_active' => true]);
    $driverUser->roles()->attach($driverRole->id);

    $this->actingAs($driverUser, 'sanctum')
        ->getJson('/api/admin/dashboard')
        ->assertStatus(403);

    $this->actingAs($driverUser, 'sanctum')
        ->getJson('/api/admin/notifications')
        ->assertStatus(403);
});
