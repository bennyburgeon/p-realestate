<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

it('lets a user update their public roles', function () {
    $user = User::factory()->create();
    $user->assignRole('buyer_tenant');

    $this->actingAs($user)
        ->patch(route('profile.roles.update'), ['roles' => ['owner', 'agent']])
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->hasRole('owner'))->toBeTrue()
        ->and($user->hasRole('agent'))->toBeTrue()
        ->and($user->hasRole('buyer_tenant'))->toBeFalse();
});

it('preserves admin and developer roles when syncing public roles', function () {
    $user = User::factory()->create();
    $user->assignRole(['admin', 'buyer_tenant']);

    $this->actingAs($user)
        ->patch(route('profile.roles.update'), ['roles' => ['owner']]);

    $user->refresh();

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasRole('owner'))->toBeTrue()
        ->and($user->hasRole('buyer_tenant'))->toBeFalse();
});

it('rejects privileged role names submitted through the public form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.roles.update'), ['roles' => ['super_admin']])
        ->assertSessionHasErrors('roles.0');

    expect($user->fresh()->hasRole('super_admin'))->toBeFalse();
});
