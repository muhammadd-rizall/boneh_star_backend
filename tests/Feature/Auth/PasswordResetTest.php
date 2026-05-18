<?php

use App\Models\Customer;
use App\Notifications\Auth\CustomerResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('customer can request a password reset link', function () {
    Notification::fake();

    $customer = Customer::factory()->create([
        'email' => 'customer@example.com',
    ]);

    $response = $this->postJson(route('auth.forgot-password'), [
        'email' => 'customer@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    Notification::assertSentTo(
        $customer,
        CustomerResetPasswordNotification::class
    );
});

test('customer cannot request a password reset link for non-existent email', function () {
    $response = $this->postJson(route('auth.forgot-password'), [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

test('customer can reset password with valid token', function () {
    $customer = Customer::factory()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('old-password'),
    ]);

    $token = Password::broker('customers')->createToken($customer);

    $response = $this->postJson(route('auth.reset-password'), [
        'token' => $token,
        'email' => 'customer@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    expect(Hash::check('new-password', $customer->fresh()->password))->toBeTrue();
});

test('customer cannot reset password with invalid token', function () {
    $customer = Customer::factory()->create([
        'email' => 'customer@example.com',
    ]);

    $response = $this->postJson(route('auth.reset-password'), [
        'token' => 'invalid-token',
        'email' => 'customer@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});
