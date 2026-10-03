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

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-sitemap', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Sitemapville', 'slug' => 'sitemapville']);
    $this->owner = tap(User::factory()->create())->assignRole('owner');
});

it('returns a valid xml sitemap containing only live properties', function () {
    $service = app(PropertyService::class);

    $live = $service->create($this->owner, [
        'title' => 'Live Sitemap Property',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '1 Sitemap Lane',
        'contact_phone' => '9000000060',
    ]);
    $service->submitForReview($live);
    $service->approve($live, $this->owner);

    $draft = $service->create($this->owner, [
        'title' => 'Draft Sitemap Property',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '2 Sitemap Lane',
        'contact_phone' => '9000000061',
    ]);

    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/xml; charset=UTF-8')
        ->assertSee(route('properties.show', $live), false)
        ->assertDontSee(route('properties.show', $draft), false);
});
