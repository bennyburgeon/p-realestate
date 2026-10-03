<?php

use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\Status;
use App\Models\User;
use App\Services\PropertyService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-sold', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Soldville', 'slug' => 'soldville']);
    $this->owner = tap(User::factory()->create())->assignRole('owner');
});

it('marks a sale property as sold with the correct terminal status', function () {
    Notification::fake();
    $service = app(PropertyService::class);

    $property = $service->create($this->owner, [
        'title' => 'Sold Sale Property',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '1 Sold Lane',
        'contact_phone' => '9000000040',
    ]);
    $service->submitForReview($property);
    $service->approve($property, $this->owner);

    $this->actingAs($this->owner)
        ->post(route('properties.mark-sold', $property), ['sold_price' => 4800000])
        ->assertRedirect(route('properties.mine'));

    expect($property->refresh()->status_id)->toBe(Status::PROPERTY_SOLD)
        ->and((float) $property->sold_price)->toBe(4800000.0);
});

it('marks a rent property as rented, not sold', function () {
    Notification::fake();
    $service = app(PropertyService::class);

    $property = $service->create($this->owner, [
        'title' => 'Rented Property',
        'listing_type' => 'rent',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'rent_amount' => 25000,
        'address' => '2 Sold Lane',
        'contact_phone' => '9000000041',
    ]);
    $service->submitForReview($property);
    $service->approve($property, $this->owner);

    $service->markAsSold($property, $this->owner, []);

    expect($property->refresh()->status_id)->toBe(Status::PROPERTY_RENTED);
});

it('removes a sold property from public search results', function () {
    Notification::fake();
    $service = app(PropertyService::class);

    $property = $service->create($this->owner, [
        'title' => 'Formerly Live Property',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '3 Sold Lane',
        'contact_phone' => '9000000042',
    ]);
    $service->submitForReview($property);
    $service->approve($property, $this->owner);

    $this->get(route('properties.index'))->assertSee('Formerly Live Property');

    $service->markAsSold($property, $this->owner, []);

    $this->get(route('properties.index'))->assertDontSee('Formerly Live Property');
});

it('prevents a non-owner from marking a property as sold', function () {
    Notification::fake();
    $service = app(PropertyService::class);
    $other = User::factory()->create();

    $property = $service->create($this->owner, [
        'title' => 'Protected Property',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '4 Sold Lane',
        'contact_phone' => '9000000043',
    ]);
    $service->submitForReview($property);
    $service->approve($property, $this->owner);

    $this->actingAs($other)
        ->post(route('properties.mark-sold', $property), [])
        ->assertForbidden();
});
