<?php

use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\PropertyMatch;
use App\Models\User;
use App\Services\PropertyRequirementService;
use App\Services\PropertyService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-match-test', 'nature' => 'residential']);
    $this->city = Location::create(['type' => 'city', 'name' => 'Matchtown', 'slug' => 'matchtown']);

    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');

    $this->buyer = User::factory()->create();
    $this->buyer->assignRole('buyer_tenant');
});

it('scores an exact-fit property as an excellent match', function () {
    $propertyService = app(PropertyService::class);
    $requirementService = app(PropertyRequirementService::class);

    $property = $propertyService->create($this->owner, [
        'title' => 'Perfectly Matching Apartment',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->city->id,
        'price' => 5000000,
        'built_up_area' => 1000,
        'bedrooms' => 2,
        'furnishing_status' => 'semi_furnished',
        'address' => '1 Match Lane',
        'contact_phone' => '9111111111',
    ]);
    $propertyService->submitForReview($property);
    $propertyService->approve($property);

    $requirement = $requirementService->create($this->buyer, [
        'title' => 'Looking for a 2BHK apartment to buy in Matchtown',
        'intent' => 'buy',
        'property_nature' => 'residential',
        'property_category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'budget_min' => 4000000,
        'budget_max' => 6000000,
        'area_min' => 800,
        'area_max' => 1200,
        'bedrooms' => 2,
        'furnishing_status' => 'semi_furnished',
        'contact_name' => $this->buyer->name,
        'contact_phone' => '9111111112',
    ]);

    $match = PropertyMatch::where('property_requirement_id', $requirement->id)
        ->where('property_id', $property->id)
        ->first();

    expect($match)->not->toBeNull()
        ->and($match->match_band)->toBe(PropertyMatch::BAND_EXCELLENT);
});

it('does not match a rental property against a buy requirement', function () {
    $propertyService = app(PropertyService::class);
    $requirementService = app(PropertyRequirementService::class);

    $property = $propertyService->create($this->owner, [
        'title' => 'Rental Only Apartment',
        'listing_type' => 'rent',
        'property_category_id' => $this->category->id,
        'location_id' => $this->city->id,
        'rent_amount' => 25000,
        'bedrooms' => 2,
        'address' => '2 Match Lane',
        'contact_phone' => '9111111113',
    ]);
    $propertyService->submitForReview($property);
    $propertyService->approve($property);

    $requirement = $requirementService->create($this->buyer, [
        'title' => 'Looking for a 2BHK apartment to buy',
        'intent' => 'buy',
        'property_nature' => 'residential',
        'property_category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'bedrooms' => 2,
        'contact_name' => $this->buyer->name,
        'contact_phone' => '9111111114',
    ]);

    $match = PropertyMatch::where('property_requirement_id', $requirement->id)
        ->where('property_id', $property->id)
        ->first();

    expect($match)->toBeNull();
});
