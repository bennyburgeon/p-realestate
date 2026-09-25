<?php

use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\Status;
use App\Models\User;
use App\Services\PropertyService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-test', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Testville', 'slug' => 'testville']);
});

function makeOwner(): User
{
    $owner = User::factory()->create();
    $owner->assignRole('owner');

    return $owner;
}

it('lets an owner create a property as a draft', function () {
    $owner = makeOwner();

    $property = app(PropertyService::class)->create($owner, [
        'title' => 'Cosy 2BHK Apartment',
        'listing_type' => 'rent',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'rent_amount' => 25000,
        'address' => '123 Test Street',
        'contact_phone' => '9999999999',
    ]);

    expect($property->status_id)->toBe(Status::PROPERTY_DRAFT)
        ->and($property->slug)->toBe('cosy-2bhk-apartment')
        ->and($property->user_id)->toBe($owner->id);
});

it('moves a property through submit and approve status transitions', function () {
    $owner = makeOwner();
    $service = app(PropertyService::class);

    $property = $service->create($owner, [
        'title' => 'Sunny Studio Flat',
        'listing_type' => 'rent',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'rent_amount' => 15000,
        'address' => '456 Test Ave',
        'contact_phone' => '9999999998',
    ]);

    $service->submitForReview($property);
    expect($property->refresh()->status_id)->toBe(Status::PROPERTY_PENDING_REVIEW);

    $service->approve($property);
    expect($property->refresh()->status_id)->toBe(Status::PROPERTY_AVAILABLE)
        ->and($property->published_at)->not->toBeNull();
});

it('prevents a non-owner from updating someone else\'s property', function () {
    $owner = makeOwner();
    $otherOwner = makeOwner();

    $property = app(PropertyService::class)->create($owner, [
        'title' => 'Private Villa',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '789 Test Blvd',
        'contact_phone' => '9999999997',
    ]);

    $this->actingAs($otherOwner)
        ->put(route('properties.update', $property), ['title' => 'Hacked title'])
        ->assertForbidden();
});

it('shows a published property on the public search results', function () {
    $owner = makeOwner();
    $service = app(PropertyService::class);

    $property = $service->create($owner, [
        'title' => 'Searchable Apartment',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 3000000,
        'address' => '1 Search Lane',
        'contact_phone' => '9999999996',
    ]);
    $service->submitForReview($property);
    $service->approve($property);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('Searchable Apartment');
});

it('does not show unapproved properties in search results', function () {
    $owner = makeOwner();

    app(PropertyService::class)->create($owner, [
        'title' => 'Hidden Draft Apartment',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 3000000,
        'address' => '2 Hidden Lane',
        'contact_phone' => '9999999995',
    ]);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertDontSee('Hidden Draft Apartment');
});
