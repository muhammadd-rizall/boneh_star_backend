<?php

use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->customer = Customer::factory()->create();
});

test('customer can delete their notification', function () {
    // Create a notification for the customer
    $notification = Notification::create([
        'target_type' => 'customer',
        'target_id' => $this->customer->id,
        'title' => 'Test Notification',
        'message' => 'This is a test notification',
        'type' => 'system',
        'is_read' => false,
    ]);

    $response = $this->actingAs($this->customer, 'sanctum')->deleteJson("/api/customer/notifications/{$notification->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Notification deleted successfully',
    ]);

    $this->assertDatabaseMissing('notifications', [
        'id' => $notification->id,
    ]);
});
