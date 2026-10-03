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

    $this->category = PropertyCategory::create(['name' => 'Villa', 'slug' => 'villa-slug', 'nature' => 'residential']);
    $this->city = Location::create(['type' => 'city', 'name' => 'Kozhikode', 'slug' => 'kozhikode']);
    $this->locality = Location::create(['type' => 'locality', 'name' => 'Pantheerankavu', 'slug' => 'pantheerankavu', 'parent_id' => $this->city->id]);
    $this->owner = tap(User::factory()->create())->assignRole('owner');
});

it('generates a city/type-aware seo slug', function () {
    $property = app(PropertyService::class)->create($this->owner, [
        'title' => '3 BHK Villa',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->locality->id,
        'price' => 5000000,
        'address' => '1 Slug Lane',
        'contact_phone' => '9000000070',
    ]);

    expect($property->slug)->toBe('3-bhk-villa-for-sale-pantheerankavu-kozhikode');
});

it('appends a numeric suffix on slug collision', function () {
    $service = app(PropertyService::class);

    $first = $service->create($this->owner, [
        'title' => 'Duplicate Title Villa',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->locality->id,
        'price' => 5000000,
        'address' => '1 Dup Lane',
        'contact_phone' => '9000000071',
    ]);

    $second = $service->create($this->owner, [
        'title' => 'Duplicate Title Villa',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->locality->id,
        'price' => 5000000,
        'address' => '2 Dup Lane',
        'contact_phone' => '9000000072',
    ]);

    expect($first->slug)->toBe('duplicate-title-villa-for-sale-pantheerankavu-kozhikode')
        ->and($second->slug)->toBe('duplicate-title-villa-for-sale-pantheerankavu-kozhikode-1');
});
