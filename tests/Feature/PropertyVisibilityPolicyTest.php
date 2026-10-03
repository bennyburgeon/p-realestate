<?php

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-vis', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Visville', 'slug' => 'visville']);
});

function makePropertyWithStatus(int $statusId): Property
{
    $owner = tap(User::factory()->create())->assignRole('owner');

    return Property::create([
        'user_id' => $owner->id,
        'property_category_id' => test()->category->id,
        'location_id' => test()->location->id,
        'status_id' => $statusId,
        'title' => 'Visibility Test Property '.$statusId,
        'slug' => 'visibility-test-'.$statusId.'-'.uniqid(),
        'listing_type' => 'sale',
        'price' => 1000000,
        'address' => 'Test Address',
        'contact_phone' => '9000000000',
    ]);
}

dataset('non_public_statuses', [
    'draft' => [fn () => Status::PROPERTY_DRAFT],
    'pending_review' => [fn () => Status::PROPERTY_PENDING_REVIEW],
    'rejected' => [fn () => Status::PROPERTY_REJECTED],
    'suspended' => [fn () => Status::PROPERTY_SUSPENDED],
    'archived' => [fn () => Status::PROPERTY_ARCHIVED],
    'changes_requested' => [fn () => Status::PROPERTY_CHANGES_REQUESTED],
]);

it('returns 403 for a guest viewing a non-public property via direct url', function (\Closure $statusId) {
    $property = makePropertyWithStatus($statusId());

    $this->get(route('properties.show', $property))->assertForbidden();
})->with('non_public_statuses');

it('returns 403 for a non-owner viewing a non-public property via direct url', function (\Closure $statusId) {
    $property = makePropertyWithStatus($statusId());
    $other = User::factory()->create();

    $this->actingAs($other)->get(route('properties.show', $property))->assertForbidden();
})->with('non_public_statuses');

it('allows the owner to view their own non-public property', function (\Closure $statusId) {
    $property = makePropertyWithStatus($statusId());

    $this->actingAs($property->owner)->get(route('properties.show', $property))->assertOk();
})->with('non_public_statuses');

it('allows an admin to view any property regardless of status', function (\Closure $statusId) {
    $property = makePropertyWithStatus($statusId());
    $admin = tap(User::factory()->create())->assignRole('admin');

    $this->actingAs($admin)->get(route('properties.show', $property))->assertOk();
})->with('non_public_statuses');

it('allows anyone to view a live property', function () {
    $property = makePropertyWithStatus(Status::PROPERTY_AVAILABLE);

    $this->get(route('properties.show', $property))->assertOk();
});
