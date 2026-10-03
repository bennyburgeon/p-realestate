<?php

use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\Status;
use App\Models\User;
use App\Notifications\PropertyChangesRequested;
use App\Notifications\PropertyRejected;
use App\Services\PropertyService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-admin-review', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Reviewville', 'slug' => 'reviewville']);
    $this->owner = tap(User::factory()->create())->assignRole('owner');
    $this->admin = tap(User::factory()->create())->assignRole('admin');

    $this->property = app(PropertyService::class)->create($this->owner, [
        'title' => 'Admin Review Test Property',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '1 Review Lane',
        'contact_phone' => '9000000020',
    ]);
    app(PropertyService::class)->submitForReview($this->property);
});

it('denies non-admins access to the admin review screen', function () {
    $this->actingAs($this->owner)
        ->get(route('admin.properties.show', $this->property))
        ->assertForbidden();
});

it('allows an admin to view the review screen', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.properties.show', $this->property))
        ->assertOk()
        ->assertSee('Admin Review Test Property');
});

it('requires a reason to reject a property', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.properties.reject', $this->property), [])
        ->assertSessionHasErrors('reason');
});

it('rejects a property with a reason and notifies the owner', function () {
    Notification::fake();

    $this->actingAs($this->admin)
        ->post(route('admin.properties.reject', $this->property), ['reason' => 'Missing documents'])
        ->assertRedirect();

    expect($this->property->refresh()->status_id)->toBe(Status::PROPERTY_REJECTED);
    Notification::assertSentTo($this->owner, PropertyRejected::class);
});

it('requires a reason to request changes', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.properties.request-changes', $this->property), [])
        ->assertSessionHasErrors('reason');
});

it('requests changes with a reason and notifies the owner', function () {
    Notification::fake();

    $this->actingAs($this->admin)
        ->post(route('admin.properties.request-changes', $this->property), ['reason' => 'Please upload ownership document'])
        ->assertRedirect();

    expect($this->property->refresh()->status_id)->toBe(Status::PROPERTY_CHANGES_REQUESTED);
    Notification::assertSentTo($this->owner, PropertyChangesRequested::class);
});
