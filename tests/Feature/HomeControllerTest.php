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
});

it('ranks popular cities by live listings including those in their localities', function () {
    $category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-home-test', 'nature' => 'residential']);
    $owner = tap(User::factory()->create())->assignRole('owner');

    Location::create(['type' => 'city', 'name' => 'Alphaville', 'slug' => 'alphaville', 'is_popular' => true]);
    $zeta = Location::create(['type' => 'city', 'name' => 'Zetaburg', 'slug' => 'zetaburg', 'is_popular' => true]);
    $zetaLocality = Location::create(['type' => 'locality', 'name' => 'Zeta North', 'slug' => 'zeta-north', 'parent_id' => $zeta->id]);
    Location::create(['type' => 'city', 'name' => 'Quietplace', 'slug' => 'quietplace']);

    $propertyService = app(PropertyService::class);
    foreach ([$zeta, $zetaLocality] as $index => $location) {
        $property = $propertyService->create($owner, [
            'title' => "Listed Apartment Number {$index}",
            'listing_type' => 'sale',
            'property_category_id' => $category->id,
            'location_id' => $location->id,
            'price' => 5000000,
            'address' => "{$index} Home Lane",
            'contact_phone' => '9222222222',
        ]);
        $propertyService->submitForReview($property);
        $propertyService->approve($property);
    }

    $this->get(route('home'))
        ->assertOk()
        ->assertViewHas('popularCities', fn ($cities) => $cities->pluck('properties_count', 'name')->all() === ['Zetaburg' => 2, 'Alphaville' => 0])
        ->assertSeeInOrder(['Zetaburg', '2 properties', 'Alphaville', '0 properties']);
});
