<?php

use App\Models\Notification;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create admin user with permissions
    $adminRole = Role::create([
        'name' => 'Super Admin',
        'slug' => 'super-admin',
        'is_active' => true,
    ]);

    $this->admin = User::factory()->create();
    $this->admin->roles()->attach($adminRole->id);
});

test('admin can delete a notification', function () {
    // Create a notification for the admin
    $notification = Notification::create([
        'target_type' => 'admin',
        'target_id' => $this->admin->id,
        'title' => 'Test Notification',
        'message' => 'This is a test notification',
        'type' => 'system',
        'is_read' => false,
    ]);

    $response = $this->actingAs($this->admin)->deleteJson("/api/admin/notifications/{$notification->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Notification deleted successfully',
    ]);

    $this->assertDatabaseMissing('notifications', [
        'id' => $notification->id,
    ]);
});
