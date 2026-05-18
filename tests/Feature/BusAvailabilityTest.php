<?php

use App\Models\ActivityType;
use App\Models\Booking;
use App\Models\BookingBus;
use App\Models\Buses;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RouteType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create admin user with permissions
    $adminRole = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
        'is_active' => true,
    ]);

    $p1 = Permission::create([
        'name' => 'Create Booking',
        'slug' => 'bookings.create',
        'group' => 'booking',
    ]);

    $p2 = Permission::create([
        'name' => 'Update Booking',
        'slug' => 'bookings.update',
        'group' => 'booking',
    ]);

    $adminRole->permissions()->attach([$p1->id, $p2->id]);

    $this->admin = User::factory()->create();
    $this->admin->roles()->attach($adminRole->id);
});

test('bus is considered unavailable if assigned to a quoted booking with overlapping dates', function () {
    // 1. Setup data
    $bus = Buses::factory()->create();
    $driver = Driver::factory()->create();
    $customer = Customer::factory()->create();
    $activity = ActivityType::factory()->create();
    $route = RouteType::factory()->create();

    $departureDate = now()->addDays(5)->format('Y-m-d H:i:s');
    $returnDate = now()->addDays(7)->format('Y-m-d H:i:s');

    // 2. Create a "quoted" booking with this bus assigned
    $booking1 = Booking::create([
        'customer_id' => $customer->id,
        'activity_id' => $activity->id,
        'route_type_id' => $route->id,
        'booking_code' => 'BK-TEST-0001',
        'departure_date' => $departureDate,
        'return_date' => $returnDate,
        'origin' => 'Origin Test',
        'passenger_count' => 10,
        'bus_count_requested' => 1,
        'status' => 'quoted',
    ]);

    BookingBus::create([
        'booking_id' => $booking1->id,
        'bus_id' => $bus->id,
        'driver_id' => $driver->id,
        'seat_count' => 40,
    ]);

    // 3. Try to create another booking with the same bus for overlapping dates
    // This should FAIL if we include 'quoted' in BusAvailabilityRule
    $response = $this->actingAs($this->admin)->postJson('/api/admin/bookings', [
        'customer_id' => $customer->id,
        'activity_id' => $activity->id,
        'route_type_id' => $route->id,
        'departure_date' => $departureDate,
        'return_date' => $returnDate,
        'origin' => 'Origin Test 2',
        'passenger_count' => 10,
        'bus_count_requested' => 1,
        'status' => 'pending',
        'buses' => [
            [
                'bus_id' => $bus->id,
                'driver_id' => $driver->id,
                'seat_count' => 40,
            ]
        ]
    ]);

    // Currently it will pass (201) because BusAvailabilityRule only checks 'confirmed' and 'ongoing'
    // We want it to fail (422)
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['buses.0.bus_id']);
});
