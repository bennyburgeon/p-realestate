<?php

use App\Livewire\PostRequirementWizard;
use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\Status;
use App\Models\User;
use App\Services\PropertyRequirementService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-req-test', 'nature' => 'residential']);
    $this->city = Location::create(['type' => 'city', 'name' => 'Requestville', 'slug' => 'requestville']);
});

function makeBuyer(): User
{
    $buyer = User::factory()->create();
    $buyer->assignRole('buyer_tenant');

    return $buyer;
}

it('creates an active requirement with a unique reference number', function () {
    $buyer = makeBuyer();

    $requirement = app(PropertyRequirementService::class)->create($buyer, [
        'title' => 'Looking for a 2BHK to rent in Requestville',
        'intent' => 'rent',
        'property_nature' => 'residential',
        'property_category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'budget_min' => 10000,
        'budget_max' => 20000,
        'contact_name' => $buyer->name,
        'contact_phone' => '9000000001',
    ]);

    expect($requirement->status_id)->toBe(Status::REQUIREMENT_ACTIVE)
        ->and($requirement->reference_number)->toStartWith('REQ-')
        ->and($requirement->expires_at)->not->toBeNull();
});

it('validates required fields on the requirement wizard first step', function () {
    $buyer = makeBuyer();

    Livewire::actingAs($buyer)
        ->test(PostRequirementWizard::class)
        ->set('title', 'short')
        ->call('nextStep')
        ->assertHasErrors(['title', 'property_category_id', 'city_id']);
});

it('lets a user submit a requirement end to end through the wizard', function () {
    $buyer = makeBuyer();

    $component = Livewire::actingAs($buyer)
        ->test(PostRequirementWizard::class)
        ->set('title', 'Looking for a 2BHK apartment to rent')
        ->set('intent', 'rent')
        ->set('property_nature', 'residential')
        ->set('property_category_id', $this->category->id)
        ->set('city_id', $this->city->id)
        ->call('nextStep')
        ->assertSet('step', 2)
        ->call('nextStep')
        ->assertSet('step', 3)
        ->set('contact_name', $buyer->name)
        ->set('contact_phone', '9000000002')
        ->call('nextStep')
        ->assertSet('step', 4)
        ->call('submit');

    $component->assertRedirect();

    $this->assertDatabaseHas('property_requirements', [
        'title' => 'Looking for a 2BHK apartment to rent',
        'status_id' => Status::REQUIREMENT_ACTIVE,
    ]);
});

it('lets the owner pause, resume and close their requirement', function () {
    $buyer = makeBuyer();
    $service = app(PropertyRequirementService::class);

    $requirement = $service->create($buyer, [
        'title' => 'Looking for an office space to lease',
        'intent' => 'lease',
        'property_nature' => 'commercial',
        'property_category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'contact_name' => $buyer->name,
        'contact_phone' => '9000000003',
    ]);

    $this->actingAs($buyer)->post(route('requirements.pause', $requirement))->assertRedirect();
    expect($requirement->refresh()->status_id)->toBe(Status::REQUIREMENT_PAUSED);

    $this->actingAs($buyer)->post(route('requirements.resume', $requirement))->assertRedirect();
    expect($requirement->refresh()->status_id)->toBe(Status::REQUIREMENT_ACTIVE);

    $this->actingAs($buyer)->post(route('requirements.close', $requirement))->assertRedirect();
    expect($requirement->refresh()->status_id)->toBe(Status::REQUIREMENT_CLOSED);
});

it('prevents another user from closing someone else\'s requirement', function () {
    $buyer = makeBuyer();
    $otherBuyer = makeBuyer();

    $requirement = app(PropertyRequirementService::class)->create($buyer, [
        'title' => 'Looking for a plot of land to buy',
        'intent' => 'buy',
        'property_nature' => 'residential',
        'property_category_id' => $this->category->id,
        'city_id' => $this->city->id,
        'contact_name' => $buyer->name,
        'contact_phone' => '9000000004',
    ]);

    $this->actingAs($otherBuyer)->post(route('requirements.close', $requirement))->assertForbidden();
});
