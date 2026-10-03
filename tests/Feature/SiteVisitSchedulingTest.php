<?php

use App\Models\Enquiry;
use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\Status;
use App\Models\User;
use App\Notifications\SiteVisitScheduled;
use App\Services\PropertyEnquiryService;
use App\Services\PropertyService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-visit', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Visitville', 'slug' => 'visitville']);
    $this->owner = tap(User::factory()->create())->assignRole('owner');
    $this->buyer = User::factory()->create(['phone' => '9000000030']);

    $this->property = app(PropertyService::class)->create($this->owner, [
        'title' => 'Site Visit Test Property',
        'listing_type' => 'sale',
        'property_category_id' => $this->category->id,
        'location_id' => $this->location->id,
        'price' => 5000000,
        'address' => '1 Visit Lane',
        'contact_phone' => '9000000031',
    ]);
    app(PropertyService::class)->submitForReview($this->property);
    app(PropertyService::class)->approve($this->property, $this->owner);

    $this->enquiry = app(PropertyEnquiryService::class)->create($this->property, [
        'name' => 'Buyer Name',
        'phone' => '9000000030',
        'message' => 'Interested',
    ], $this->buyer);
});

it('lets the property owner schedule a site visit for an enquiry', function () {
    Notification::fake();

    $this->actingAs($this->owner)
        ->post(route('enquiries.schedule-visit', $this->enquiry), [
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ])
        ->assertRedirect();

    expect($this->enquiry->refresh()->status_id)->toBe(Status::ENQUIRY_VISIT_SCHEDULED)
        ->and($this->property->siteVisits()->count())->toBe(1);

    Notification::assertSentTo($this->buyer, SiteVisitScheduled::class);
});

it('prevents a non-owner from scheduling a site visit', function () {
    $other = User::factory()->create();

    $this->actingAs($other)
        ->post(route('enquiries.schedule-visit', $this->enquiry), [
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ])
        ->assertForbidden();
});
