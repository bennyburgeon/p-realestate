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

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-history', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Historyville', 'slug' => 'historyville']);
    $this->owner = tap(User::factory()->create())->assignRole('owner');
    $this->admin = tap(User::factory()->create())->assignRole('admin');
});

function makeHistoryTestProperty(): \App\Models\Property
{
    return app(PropertyService::class)->create(test()->owner, [
        'title' => 'History Test Property',
        'listing_type' => 'sale',
        'property_category_id' => test()->category->id,
        'location_id' => test()->location->id,
        'price' => 5000000,
        'address' => '1 History Lane',
        'contact_phone' => '9000000010',
    ]);
}

it('logs a history row on approve', function () {
    Notification::fake();
    $service = app(PropertyService::class);
    $property = makeHistoryTestProperty();
    $service->submitForReview($property);

    $service->approve($property, $this->admin);

    $entry = $property->statusHistories()->first();
    expect($entry->to_status_id)->toBe(Status::PROPERTY_AVAILABLE)
        ->and($entry->from_status_id)->toBe(Status::PROPERTY_PENDING_REVIEW)
        ->and($entry->user_id)->toBe($this->admin->id);
});

it('logs a history row with reason on reject', function () {
    Notification::fake();
    $service = app(PropertyService::class);
    $property = makeHistoryTestProperty();
    $service->submitForReview($property);

    $service->reject($property, $this->admin, 'Incomplete documents');

    $entry = $property->statusHistories()->first();
    expect($entry->to_status_id)->toBe(Status::PROPERTY_REJECTED)
        ->and($entry->reason)->toBe('Incomplete documents');
});

it('logs a history row on request changes', function () {
    Notification::fake();
    $service = app(PropertyService::class);
    $property = makeHistoryTestProperty();
    $service->submitForReview($property);

    $service->requestChanges($property, $this->admin, 'Please upload ownership document');

    $entry = $property->statusHistories()->first();
    expect($entry->to_status_id)->toBe(Status::PROPERTY_CHANGES_REQUESTED)
        ->and($entry->reason)->toBe('Please upload ownership document');
});

it('logs a history row on mark as sold', function () {
    Notification::fake();
    $service = app(PropertyService::class);
    $property = makeHistoryTestProperty();
    $service->submitForReview($property);
    $service->approve($property, $this->admin);

    $service->markAsSold($property, $this->owner, ['sold_price' => 4900000]);

    $entry = $property->statusHistories()->first();
    expect($entry->to_status_id)->toBe(Status::PROPERTY_SOLD)
        ->and($property->refresh()->sold_price)->toEqual(4900000);
});
