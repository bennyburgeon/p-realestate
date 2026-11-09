<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

it('suspends a user when an admin toggles them', function () {
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.toggle-active', $user))
        ->assertSessionHas('status', 'User suspended.');

    expect($user->fresh()->is_active)->toBeFalse();
});

it('rejects a suspended user at password login', function () {
    $user = User::factory()->create();
    $user->forceFill(['is_active' => false])->save();

    $this->post('/login/password', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => 'This account has been suspended. Please contact support.']);

    $this->assertGuest();
});
