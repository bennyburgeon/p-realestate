<?php

namespace Tests\Feature\Auth;

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+91 90000 00099',
            'account_type' => 'buyer_tenant',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registered_user_is_assigned_the_selected_role(): void
    {
        $this->post('/register', [
            'name' => 'Owner User',
            'email' => 'owner-signup@example.com',
            'phone' => '+91 90000 00098',
            'account_type' => 'owner',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertTrue(auth()->user()->hasRole('owner'));
    }
}
