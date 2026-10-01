<?php

use App\Models\ActivityType;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Quotation;
use App\Models\RouteType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');

    $this->customer = Customer::factory()->create();
    $this->admin = User::factory()->create(['is_active' => true]);

    $activityType = ActivityType::create(['name' => 'Wisata']);
    $routeType = RouteType::create(['name' => 'Dalam Kota', 'scope' => 'inside']);

    $this->booking = Booking::create([
        'customer_id' => $this->customer->id,
        'activity_id' => $activityType->id,
        'route_type_id' => $routeType->id,
        'booking_code' => 'BK-99001',
        'origin' => 'Surabaya',
        'destination' => 'Batu',
        'departure_date' => now()->addDays(10)->format('Y-m-d'),
        'passenger_count' => 20,
        'bus_count_requested' => 1,
        'status' => 'quoted',
    ]);

    $quotation = Quotation::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->admin->id,
        'proposed_price' => 2000000,
        'dp_amount' => 500000,
        'note_to_customer' => 'Test quote',
        'valid_until' => now()->addDays(3),
        'status' => 'accepted',
    ]);

    $this->transaction = Transaction::create([
        'booking_id' => $this->booking->id,
        'quotation_id' => $quotation->id,
        'total_amount' => 2000000,
        'dp_amount' => 500000,
        'dp_paid' => 0,
        'remaining_amount' => 2000000,
        'status' => 'waiting',
    ]);

    $this->paymentMethod = PaymentMethod::create([
        'bank_name' => 'BCA',
        'account_number' => '9876543210',
        'account_name' => 'PT Boneh Star',
        'is_active' => true,
    ]);
});

test('customer cannot upload payment amount greater than remaining amount', function () {
    $this->actingAs($this->customer, 'sanctum');

    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 3000000, // Remaining is only 2000000
        'payment_type' => 'dp',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);
});

test('customer cannot upload payment when transaction is already fully paid', function () {
    $this->transaction->update([
        'remaining_amount' => 0,
        'status' => 'fully_paid',
    ]);

    $this->actingAs($this->customer, 'sanctum');

    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 500000,
        'payment_type' => 'settlement',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);
});

test('customer can upload valid payment within remaining amount limit', function () {
    $this->actingAs($this->customer, 'sanctum');

    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 500000,
        'payment_type' => 'dp',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response->assertStatus(200);
});

test('customer cannot upload payment amount different from required dp amount', function () {
    $this->actingAs($this->customer, 'sanctum');

    $file = UploadedFile::fake()->image('receipt.jpg');

    // Case 1: Less than required DP
    $response = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 300000, // dp_amount is 500000
        'payment_type' => 'dp',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);

    // Case 2: Greater than required DP
    $response2 = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 600000, // dp_amount is 500000
        'payment_type' => 'dp',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response2->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);
});

test('customer cannot upload payment amount different from remaining amount for settlement', function () {
    $this->actingAs($this->customer, 'sanctum');

    $file = UploadedFile::fake()->image('receipt.jpg');

    $response = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 1500000, // remaining_amount is 2000000
        'payment_type' => 'settlement',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);
});

test('customer cannot upload another dp if one is already pending or verified', function () {
    // Create an existing DP payment
    $this->transaction->payments()->create([
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 500000,
        'payment_type' => 'dp',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => 'receipt.jpg',
        'status' => 'pending',
    ]);

    $this->actingAs($this->customer, 'sanctum');

    $file = UploadedFile::fake()->image('receipt2.jpg');

    $response = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 500000,
        'payment_type' => 'dp',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['payment_type']);
});

test('customer cannot upload another payment if a settlement is already pending or verified', function () {
    // Create an existing Settlement payment
    $this->transaction->payments()->create([
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 2000000,
        'payment_type' => 'settlement',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => 'receipt.jpg',
        'status' => 'pending',
    ]);

    $this->actingAs($this->customer, 'sanctum');

    $file = UploadedFile::fake()->image('receipt2.jpg');

    $response = $this->postJson("/api/customer/bookings/{$this->booking->id}/upload-payment", [
        'payment_method_id' => $this->paymentMethod->id,
        'amount' => 500000,
        'payment_type' => 'dp',
        'payment_date' => now()->format('Y-m-d'),
        'transfer_proof' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['payment_type']);
});
