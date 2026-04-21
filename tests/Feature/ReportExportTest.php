<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can download booking report as excel', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'View Bookings', 'slug' => 'bookings.view', 'group' => 'bookings']);
    $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $role->permissions()->attach($permission);
    $user->roles()->attach($role);
    $this->actingAs($user);

    $response = $this->get('/api/admin/reports/bookings/excel?start_date=2020-01-01&end_date=2020-12-31');

    $response->assertStatus(200)
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('admin can download income report as pdf', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'View Payments', 'slug' => 'payments.view', 'group' => 'payments']);
    $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $role->permissions()->attach($permission);
    $user->roles()->attach($role);
    $this->actingAs($user);

    $response = $this->get('/api/admin/reports/income/pdf?month=1&year=2020');

    $response->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');
});
