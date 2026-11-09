<?php

use App\Livewire\PostRequirementWizard;
use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\PropertyRequirement;
use App\Models\User;
use App\Services\PropertyRequirementService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Apartment', 'slug' => 'apartment-wizard-areas', 'nature' => 'residential']);
    $this->kochi = Location::create(['type' => 'city', 'name' => 'Kochi', 'slug' => 'kochi-ernakulam', 'district' => 'Ernakulam']);
    $this->aluva = Location::create(['type' => 'city', 'name' => 'Aluva', 'slug' => 'aluva-ernakulam', 'district' => 'Ernakulam']);
    $this->kakkanad = Location::create(['type' => 'locality', 'name' => 'Kakkanad', 'slug' => 'kakkanad', 'parent_id' => $this->kochi->id]);
    $this->kollam = Location::create(['type' => 'city', 'name' => 'Kollam', 'slug' => 'kollam-kollam', 'district' => 'Kollam']);

    $this->buyer = tap(User::factory()->create(['phone' => '9000000002']))->assignRole('buyer_tenant');
});

function wizardAtLocationStep(User $buyer, PropertyCategory $category): Testable
{
    return Livewire::actingAs($buyer)
        ->test(PostRequirementWizard::class)
        ->call('nextStep')
        ->set('property_category_id', $category->id)
        ->set('title', 'Looking for a flat in Kochi')
        ->call('nextStep')
        ->assertSet('step', 3);
}

it('offers the city localities and same-district local bodies as preferred areas', function () {
    wizardAtLocationStep($this->buyer, $this->category)
        ->set('city_id', $this->kochi->id)
        ->assertViewHas('preferredAreas', fn ($areas) => $areas->pluck('name')->all() === ['Kakkanad', 'Aluva'])
        ->assertSee('Search and add areas');
});

it('saves the chosen preferred areas with the requirement', function () {
    wizardAtLocationStep($this->buyer, $this->category)
        ->set('city_id', $this->kochi->id)
        ->set('preferred_locality_ids', [$this->kakkanad->id, $this->aluva->id])
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('step', 4);

    expect(PropertyRequirement::sole()->preferred_locality_ids)->toBe([$this->kakkanad->id, $this->aluva->id]);
});

it('rejects a preferred area outside the chosen city and district', function () {
    wizardAtLocationStep($this->buyer, $this->category)
        ->set('city_id', $this->kochi->id)
        ->set('preferred_locality_ids', [$this->kollam->id])
        ->call('nextStep')
        ->assertHasErrors(['preferred_locality_ids.0' => 'in'])
        ->assertSet('step', 3);
});

it('drops preferred areas that do not belong to a newly chosen city', function () {
    wizardAtLocationStep($this->buyer, $this->category)
        ->set('city_id', $this->kochi->id)
        ->set('preferred_locality_ids', [$this->kakkanad->id])
        ->set('city_id', $this->kollam->id)
        ->assertSet('preferred_locality_ids', []);
});

it('explains when a city has no areas to choose from', function () {
    $loneCity = Location::create(['type' => 'city', 'name' => 'Lonetown', 'slug' => 'lonetown']);

    wizardAtLocationStep($this->buyer, $this->category)
        ->set('city_id', $loneCity->id)
        ->assertSee('No areas are listed for Lonetown yet');
});

it('loads saved preferred areas when editing a requirement', function () {
    $requirement = app(PropertyRequirementService::class)->create($this->buyer, [
        'title' => 'Looking for a flat in Kochi',
        'intent' => 'buy',
        'property_nature' => 'residential',
        'property_category_id' => $this->category->id,
        'city_id' => $this->kochi->id,
        'preferred_locality_ids' => [$this->aluva->id],
        'contact_name' => $this->buyer->name,
        'contact_phone' => '9000000002',
    ]);

    Livewire::actingAs($this->buyer)
        ->test(PostRequirementWizard::class, ['propertyRequirement' => $requirement])
        ->assertSet('city_id', $this->kochi->id)
        ->assertSet('preferred_locality_ids', [$this->aluva->id]);
});

it('clears the category when the property nature changes', function () {
    $office = PropertyCategory::create(['name' => 'Office', 'slug' => 'office-wizard-areas', 'nature' => 'commercial']);

    Livewire::actingAs($this->buyer)
        ->test(PostRequirementWizard::class)
        ->set('property_category_id', $this->category->id)
        ->set('property_nature', 'commercial')
        ->assertSet('property_category_id', null)
        ->set('property_category_id', $office->id)
        ->set('property_nature', 'commercial')
        ->assertSet('property_category_id', $office->id);
});
