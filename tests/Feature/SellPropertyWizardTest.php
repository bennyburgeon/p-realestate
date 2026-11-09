<?php

use App\Livewire\SellPropertyWizard;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(StatusSeeder::class);

    $this->category = PropertyCategory::create(['name' => 'Villa', 'slug' => 'villa-wizard', 'nature' => 'residential']);
    $this->location = Location::create(['type' => 'city', 'name' => 'Wizardville', 'slug' => 'wizardville']);
    $this->owner = tap(User::factory()->create(['phone' => '9000000001']))->assignRole('owner');
});

it('walks through all 8 steps and submits a property for review', function () {
    Notification::fake();

    Livewire::actingAs($this->owner)
        ->test(SellPropertyWizard::class)
        ->set('listing_type', 'sale')
        ->set('property_nature', 'residential')
        ->set('property_category_id', $this->category->id)
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('location_id', $this->location->id)
        ->set('address', '123 Wizard Lane')
        ->call('nextStep')
        ->assertSet('step', 3)
        ->set('bedrooms', 3)
        ->set('bathrooms', 2)
        ->set('built_up_area', '1200')
        ->call('nextStep')
        ->assertSet('step', 4)
        ->set('price', '6500000')
        ->call('nextStep')
        ->assertSet('step', 5)
        ->set('photos', [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg'),
            UploadedFile::fake()->image('photo3.jpg'),
        ])
        ->call('nextStep')
        ->assertSet('step', 6)
        ->set('contact_phone', '9000000001')
        ->call('nextStep')
        ->assertSet('step', 7)
        ->set('title', 'Beautiful 3 BHK Villa For Sale')
        ->set('description', 'A lovely villa with a garden.')
        ->call('nextStep')
        ->assertSet('step', 8)
        ->call('submit')
        ->assertRedirect();

    $property = Property::where('title', 'Beautiful 3 BHK Villa For Sale')->first();

    expect($property)->not->toBeNull()
        ->and($property->status_id)->toBe(Status::PROPERTY_PENDING_REVIEW)
        ->and($property->getMedia('images'))->toHaveCount(3);
});

it('requires at least 3 photos before continuing past the photos step', function () {
    Livewire::actingAs($this->owner)
        ->test(SellPropertyWizard::class)
        ->set('listing_type', 'sale')
        ->set('property_nature', 'residential')
        ->set('property_category_id', $this->category->id)
        ->set('location_id', $this->location->id)
        ->set('address', '123 Wizard Lane')
        ->set('price', '6500000')
        ->set('step', 5)
        ->set('photos', [UploadedFile::fake()->image('photo1.jpg')])
        ->call('nextStep')
        ->assertHasErrors('photos');
});

it('clears the property type when the nature changes to one it does not belong to', function () {
    Livewire::actingAs($this->owner)
        ->test(SellPropertyWizard::class)
        ->set('property_category_id', $this->category->id)
        ->set('property_nature', 'commercial')
        ->assertSet('property_category_id', null);
});

it('accepts a locality as the property location and shows it with its city', function () {
    $locality = Location::create(['type' => 'locality', 'name' => 'Wizard Heights', 'slug' => 'wizard-heights', 'parent_id' => $this->location->id]);

    Livewire::actingAs($this->owner)
        ->test(SellPropertyWizard::class)
        ->set('property_category_id', $this->category->id)
        ->call('nextStep')
        ->set('location_id', $locality->id)
        ->assertSee('Wizard Heights, Wizardville')
        ->set('address', '9 Wizard Lane')
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('step', 3);
});
