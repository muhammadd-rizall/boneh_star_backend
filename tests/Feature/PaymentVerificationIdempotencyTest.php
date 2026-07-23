<?php

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $adminRole = Role::create([
        'name' => 'Super Admin',
        'slug' => 'super-admin',
        'is_active' => true,
    ]);

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->roles()->attach($adminRole->id);

    $this->customer = Customer::factory()->create();

    $activityType = \App\Models\ActivityType::create(['name' => 'Wisata']);
    $routeType = \App\Models\RouteType::create(['name' => 'Dalam Kota', 'scope' => 'inside']);

    $this->booking = Booking::create([
        'customer_id' => $this->customer->id,
        'activity_id' => $activityType->id,
        'route_type_id' => $routeType->id,
        'booking_code' => 'BK-10001',
        'origin' => 'Surabaya',
        'destination' => 'Malang',
        'departure_date' => '2026-08-01',
        'passenger_count' => 30,
        'bus_count_requested' => 1,
        'status' => 'quoted',
    ]);

    $quotation = \App\Models\Quotation::create([
        'booking_id' => $this->booking->id,
        'user_id' => $this->admin->id,
        'proposed_price' => 3000000,
        'dp_amount' => 1000000,
        'note_to_customer' => 'Penawaran bus pariwisata',
        'valid_until' => now()->addDays(3),
        'status' => 'accepted',
    ]);

    $this->transaction = Transaction::create([
        'booking_id' => $this->booking->id,
        'quotation_id' => $quotation->id,
        'total_amount' => 3000000,
        'dp_amount' => 1000000,
        'dp_paid' => 0,
        'remaining_amount' => 3000000,
        'status' => 'waiting',
    ]);

    $this->paymentMethod = PaymentMethod::create([
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_name' => 'PT Boneh Star',
        'is_active' => true,
    ]);

    $this->payment = Payment::create([
        'transaction_id' => $this->transaction->id,
        'payment_method_id' => $this->paymentMethod->id,
        'payment_type' => 'dp',
        'amount' => 1000000,
        'payment_date' => now(),
        'transfer_proof' => 'payments/test.jpg',
        'status' => 'pending',
    ]);
});

test('verifying a payment updates transaction dp_paid and remaining_amount once', function () {
    $paymentService = app(PaymentService::class);

    $this->actingAs($this->admin);

    // 1st verification
    $paymentService->verifyPayment($this->payment->id, ['status' => 'verified']);

    $this->transaction->refresh();
    expect((float) $this->transaction->dp_paid)->toBe(1000000.0);
    expect((float) $this->transaction->remaining_amount)->toBe(2000000.0);
    expect($this->transaction->status)->toBe('dp_paid');

    // 2nd verification (duplicate click / duplicate API call)
    $paymentService->verifyPayment($this->payment->id, ['status' => 'verified']);

    $this->transaction->refresh();
    // Amounts must NOT be double counted!
    expect((float) $this->transaction->dp_paid)->toBe(1000000.0);
    expect((float) $this->transaction->remaining_amount)->toBe(2000000.0);
    expect($this->transaction->status)->toBe('dp_paid');
});

test('rejecting a previously verified payment reverts transaction amounts', function () {
    $paymentService = app(PaymentService::class);

    $this->actingAs($this->admin);

    // Verify first
    $paymentService->verifyPayment($this->payment->id, ['status' => 'verified']);
    $this->transaction->refresh();
    expect((float) $this->transaction->dp_paid)->toBe(1000000.0);

    // Reject previously verified payment
    $paymentService->verifyPayment($this->payment->id, [
        'status' => 'rejected',
        'rejection_reason' => 'Transfer proof fake',
    ]);

    $this->transaction->refresh();
    expect((float) $this->transaction->dp_paid)->toBe(0.0);
    expect((float) $this->transaction->remaining_amount)->toBe(3000000.0);
    expect($this->transaction->status)->toBe('waiting');
});
