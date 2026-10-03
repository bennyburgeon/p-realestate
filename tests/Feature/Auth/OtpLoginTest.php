<?php

use App\Models\OneTimePassword;
use App\Models\User;

it('renders the otp login screen', function () {
    $this->get('/login')->assertStatus(200);
});

it('stores an otp in the database for a valid phone number', function () {
    $this->post('/login/otp', ['phone' => '9876543210'])->assertRedirect(route('login'));

    $this->assertDatabaseHas('one_time_passwords', ['phone' => '9876543210', 'code' => '1234']);
});

it('rejects an invalid phone format', function () {
    $this->post('/login/otp', ['phone' => '12345'])
        ->assertSessionHasErrors('phone');
});

it('is rate limited after sending too many otps', function () {
    config(['otp.max_sends_per_window' => 2]);

    $this->post('/login/otp', ['phone' => '9876543211']);
    $this->post('/login/otp', ['phone' => '9876543211']);

    $this->post('/login/otp', ['phone' => '9876543211'])
        ->assertSessionHasErrors('phone');
});

it('logs in an existing user by phone with the correct code', function () {
    $user = User::factory()->create(['phone' => '9876543212']);

    $this->post('/login/otp', ['phone' => '9876543212']);

    $this->post('/login/otp/verify', ['phone' => '9876543212', 'code' => '1234'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

it('creates a new user when the phone number is unknown', function () {
    $this->post('/login/otp', ['phone' => '9876543213']);

    $this->post('/login/otp/verify', [
        'phone' => '9876543213',
        'code' => '1234',
        'name' => 'New Buyer',
    ])->assertRedirect(route('profile.edit'));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['phone' => '9876543213', 'name' => 'New Buyer']);
});

it('rejects an incorrect otp code', function () {
    $this->post('/login/otp', ['phone' => '9876543214']);

    $this->post('/login/otp/verify', ['phone' => '9876543214', 'code' => '0000'])
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('is rate limited after too many verify attempts', function () {
    config(['otp.max_verify_attempts' => 2]);

    $this->post('/login/otp', ['phone' => '9876543215']);

    $this->post('/login/otp/verify', ['phone' => '9876543215', 'code' => '0000']);
    $this->post('/login/otp/verify', ['phone' => '9876543215', 'code' => '0000']);

    $this->post('/login/otp/verify', ['phone' => '9876543215', 'code' => '1234'])
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});
