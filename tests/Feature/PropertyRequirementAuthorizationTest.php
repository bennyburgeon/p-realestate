<?php

use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\User;
use App\Services\PropertyRequirementService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-authz-test', 'nature' => 'residential']);
    $this->city = Location::create(['type' => 'city', 'name' => 'Authzville', 'slug' => 'authzville']);
});

function makeRequirementOwner(): User
{
    $user = User::factory()->create();
    $user->assignRole('buyer_tenant');

    return $user;
}

it('prevents another user from editing someone else\'s requirement', function () {
    $owner = makeRequirementOwner();
    $other = makeRequirementOwner();

    $requirement = app(PropertyRequirementService::class)->create($owner, [
        'title' => 'Looking for a 2BHK to buy in Authzville',
        'intent' => 'buy',
        'property_nature' => 'residential',
        'property_category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'contact_name' => $owner->name,
        'contact_phone' => '9000000010',
    ]);

    $this->actingAs($other)->get(route('requirements.edit', $requirement))->assertForbidden();
});

it('allows an admin to edit any requirement', function () {
    $owner = makeRequirementOwner();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $requirement = app(PropertyRequirementService::class)->create($owner, [
        'title' => 'Looking for a villa to rent in Authzville',
        'intent' => 'rent',
        'property_nature' => 'residential',
        'property_category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'contact_name' => $owner->name,
        'contact_phone' => '9000000011',
    ]);

    $this->actingAs($admin)->get(route('requirements.edit', $requirement))->assertOk();
});
