<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);
});

it('blocks non-admin users from the admin panel', function () {
    $buyer = User::factory()->create();
    $buyer->assignRole('buyer_tenant');

    $this->actingAs($buyer)->get(route('admin.dashboard'))->assertForbidden();
});

it('allows super admins into the admin panel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
});

it('redirects guests trying to reach the dashboard to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('lets an authenticated buyer view their dashboard', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $buyer->assignRole('buyer_tenant');

    $this->actingAs($buyer)->get(route('dashboard'))->assertOk();
});
