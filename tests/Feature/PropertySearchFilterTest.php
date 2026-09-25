<?php

use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\User;
use App\Services\PropertyService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');

    $this->apartments = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-search-test', 'nature' => 'residential']);
    $this->offices = PropertyCategory::create(['name' => 'Office', 'slug' => 'office-search-test', 'nature' => 'commercial']);
    $this->city = Location::create(['type' => 'city', 'name' => 'Filtertown', 'slug' => 'filtertown']);

    $service = app(PropertyService::class);

    $this->saleApartment = $service->create($this->owner, [
        'title' => 'Affordable Sale Apartment',
        'listing_type' => 'sale',
        'property_category_id' => $this->apartments->id,
        'location_id' => $this->city->id,
        'price' => 2000000,
        'bedrooms' => 2,
        'address' => '1 Filter Lane',
        'contact_phone' => '9222222221',
    ]);
    $service->submitForReview($this->saleApartment);
    $service->approve($this->saleApartment);

    $this->rentOffice = $service->create($this->owner, [
        'title' => 'Premium Rental Office',
        'listing_type' => 'rent',
        'property_category_id' => $this->offices->id,
        'location_id' => $this->city->id,
        'rent_amount' => 90000,
        'address' => '2 Filter Lane',
        'contact_phone' => '9222222222',
    ]);
    $service->submitForReview($this->rentOffice);
    $service->approve($this->rentOffice);
});

it('filters properties by intent', function () {
    $this->get(route('properties.index', ['intent' => 'buy']))
        ->assertOk()
        ->assertSee('Affordable Sale Apartment')
        ->assertDontSee('Premium Rental Office');
});

it('filters properties by category', function () {
    $this->get(route('properties.index', ['category' => $this->offices->slug]))
        ->assertOk()
        ->assertSee('Premium Rental Office')
        ->assertDontSee('Affordable Sale Apartment');
});

it('filters properties by price range', function () {
    $this->get(route('properties.index', ['price_max' => 1000000]))
        ->assertOk()
        ->assertDontSee('Affordable Sale Apartment');

    $this->get(route('properties.index', ['price_max' => 3000000]))
        ->assertOk()
        ->assertSee('Affordable Sale Apartment');
});
