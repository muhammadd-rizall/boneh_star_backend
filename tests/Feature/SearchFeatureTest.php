<?php

use App\Models\ActivityType;
use App\Models\Booking;
use App\Models\BusCategory;
use App\Models\Buses;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RouteType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can search buses by name or plat number', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'View Buses', 'slug' => 'buses.view', 'group' => 'buses']);
    $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $role->permissions()->attach($permission);
    $user->roles()->attach($role);
    $this->actingAs($user);

    $category = BusCategory::create(['name' => 'Big Bus', 'min_capacity' => 40, 'max_capacity' => 50]);

    Buses::create([
        'bus_category_id' => $category->id,
        'plat_number' => 'B 1234 AAA',
        'name' => 'Mercedes Benz',
        'seat_count' => 50,
        'production_year' => 2020,
        'status' => 'available',
    ]);

    Buses::create([
        'bus_category_id' => $category->id,
        'plat_number' => 'D 5678 BBB',
        'name' => 'Scania K410',
        'seat_count' => 50,
        'production_year' => 2021,
        'status' => 'available',
    ]);

    // Search by name
    $response = $this->getJson('/api/admin/buses?search=Scania');
    $response->assertStatus(200);
    $data = $response->json('data.data');
    expect($data)->toHaveCount(1);
    expect($data[0]['name'])->toBe('Scania K410');

    // Search by plat number
    $response = $this->getJson('/api/admin/buses?search=AAA');
    $response->assertStatus(200);
    $data = $response->json('data.data');
    expect($data)->toHaveCount(1);
    expect($data[0]['plat_number'])->toBe('B 1234 AAA');
});

test('admin can search bookings by booking code or customer name', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'View Bookings', 'slug' => 'bookings.view', 'group' => 'bookings']);
    $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $role->permissions()->attach($permission);
    $user->roles()->attach($role);
    $this->actingAs($user);

    $customer1 = Customer::create(['name' => 'Budi Sudarsono', 'email' => 'budi@test.com', 'phone' => '123', 'password' => '123', 'address' => 'Jakarta']);
    $customer2 = Customer::create(['name' => 'Ani Yudhoyono', 'email' => 'ani@test.com', 'phone' => '456', 'password' => '123', 'address' => 'Jakarta']);

    $activity = ActivityType::create(['name' => 'Wisata', 'description' => 'Wisata']);
    $routeType = RouteType::create(['name' => 'Dalam Kota', 'scope' => 'inside', 'description' => 'Dalam Kota']);

    Booking::create([
        'customer_id' => $customer1->id,
        'activity_id' => $activity->id,
        'route_type_id' => $routeType->id,
        'booking_code' => 'BK-99999',
        'departure_date' => now(),
        'origin' => 'Jakarta',
        'passenger_count' => 10,
        'bus_count_requested' => 1,
        'status' => 'pending',
    ]);

    Booking::create([
        'customer_id' => $customer2->id,
        'activity_id' => $activity->id,
        'route_type_id' => $routeType->id,
        'booking_code' => 'BK-11111',
        'departure_date' => now(),
        'origin' => 'Bandung',
        'passenger_count' => 10,
        'bus_count_requested' => 1,
        'status' => 'pending',
    ]);

    // Search by booking code
    $response = $this->getJson('/api/admin/bookings?search=99999');
    $response->assertStatus(200);
    $data = $response->json('data.data');
    expect($data)->toHaveCount(1);
    expect($data[0]['booking_code'])->toBe('BK-99999');

    // Search by customer name
    $response = $this->getJson('/api/admin/bookings?search=Ani');
    $response->assertStatus(200);
    $data = $response->json('data.data');
    expect($data)->toHaveCount(1);
    expect($data[0]['customer']['name'])->toBe('Ani Yudhoyono');
});
