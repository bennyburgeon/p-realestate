<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

it('renders the dedicated admin login screen without public nav', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
    $response->assertDontSee('Post Requirement');
});

it('logs an admin in and redirects to the admin dashboard', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');

    $response = $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard', absolute: false));
    $this->assertAuthenticatedAs($admin);
});

it('rejects a non admin at the admin login and logs them out', function () {
    $buyer = User::factory()->create(['password' => bcrypt('password')]);
    $buyer->assignRole('buyer_tenant');

    $response = $this->post('/admin/login', [
        'email' => $buyer->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('still blocks a public otp session from the admin dashboard', function () {
    $buyer = User::factory()->create();
    $buyer->assignRole('buyer_tenant');

    $this->actingAs($buyer)->get(route('admin.dashboard'))->assertForbidden();
});
