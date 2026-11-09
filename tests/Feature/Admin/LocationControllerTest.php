<?php

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

it('adds a locality that inherits its city region', function () {
    $admin = tap(User::factory()->create())->assignRole('admin');
    $city = Location::create(['type' => 'city', 'name' => 'Kochi', 'slug' => 'kochi-admin', 'district' => 'Ernakulam', 'state' => 'Kerala', 'country' => 'India']);

    $this->actingAs($admin)
        ->post(route('admin.locations.store'), ['type' => 'locality', 'name' => 'Edappally', 'parent_id' => $city->id])
        ->assertSessionHas('status', 'Location added.');

    expect(Location::where('name', 'Edappally')->sole())
        ->parent_id->toBe($city->id)
        ->district->toBe('Ernakulam')
        ->state->toBe('Kerala')
        ->country->toBe('India');
});

it('stores the district given for a new city', function () {
    $admin = tap(User::factory()->create())->assignRole('admin');

    $this->actingAs($admin)
        ->post(route('admin.locations.store'), ['type' => 'city', 'name' => 'Aluva', 'district' => 'Ernakulam', 'state' => 'Kerala'])
        ->assertSessionHas('status', 'Location added.');

    expect(Location::where('name', 'Aluva')->sole())
        ->parent_id->toBeNull()
        ->district->toBe('Ernakulam');
});
