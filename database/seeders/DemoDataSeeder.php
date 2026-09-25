<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\PropertyRequirement;
use App\Models\User;
use App\Services\PropertyRequirementService;
use App\Services\PropertyService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@realestate.test'],
            ['name' => 'Platform Admin', 'password' => bcrypt('password'), 'phone' => '+91 90000 00001']
        );
        $admin->assignRole('super_admin');

        $owner = User::firstOrCreate(
            ['email' => 'owner@realestate.test'],
            ['name' => 'Asha Owner', 'password' => bcrypt('password'), 'phone' => '+91 90000 00002']
        );
        $owner->assignRole('owner');

        $agent = User::firstOrCreate(
            ['email' => 'agent@realestate.test'],
            ['name' => 'Rahul Agent', 'password' => bcrypt('password'), 'phone' => '+91 90000 00003']
        );
        $agent->assignRole('agent');

        $buyer = User::firstOrCreate(
            ['email' => 'buyer@realestate.test'],
            ['name' => 'Priya Buyer', 'password' => bcrypt('password'), 'phone' => '+91 90000 00004']
        );
        $buyer->assignRole('buyer_tenant');

        $propertyService = app(PropertyService::class);
        $requirementService = app(PropertyRequirementService::class);

        $apartmentCategory = PropertyCategory::where('slug', 'apartment-flat')->first();
        $villaCategory = PropertyCategory::where('slug', 'independent-house-villa')->first();
        $officeCategory = PropertyCategory::where('slug', 'office-space')->first();

        $andheri = Location::where('slug', 'andheri-west')->first();
        $koramangala = Location::where('slug', 'koramangala')->first();
        $whitefield = Location::where('slug', 'whitefield')->first();
        $bengaluru = Location::where('slug', 'bengaluru')->first();

        $amenityIds = Amenity::inRandomOrder()->take(4)->pluck('id')->all();

        $listings = [
            [
                'user' => $owner,
                'title' => '3 BHK Sea-View Apartment in Andheri West',
                'listing_type' => 'sale',
                'property_category_id' => $apartmentCategory->id,
                'location_id' => $andheri->id,
                'price' => 28500000,
                'built_up_area' => 1450,
                'carpet_area' => 1200,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'furnishing_status' => 'semi_furnished',
                'floor_number' => 12,
                'total_floors' => 20,
                'facing' => 'west',
                'possession_status' => 'ready_to_move',
                'address' => 'Near Andheri Station, Andheri West',
                'landmark' => 'Andheri Station',
                'contact_phone' => $owner->phone,
                'contact_email' => $owner->email,
                'featured' => true,
            ],
            [
                'user' => $agent,
                'title' => 'Modern 2 BHK for Rent in Koramangala',
                'listing_type' => 'rent',
                'property_category_id' => $apartmentCategory->id,
                'location_id' => $koramangala->id,
                'rent_amount' => 48000,
                'security_deposit' => 150000,
                'built_up_area' => 1050,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'furnishing_status' => 'furnished',
                'floor_number' => 4,
                'total_floors' => 8,
                'possession_status' => 'ready_to_move',
                'address' => '5th Block, Koramangala',
                'contact_phone' => $agent->phone,
                'contact_email' => $agent->email,
                'featured' => true,
            ],
            [
                'user' => $owner,
                'title' => 'Independent Villa with Garden in Whitefield',
                'listing_type' => 'sale',
                'property_category_id' => $villaCategory->id,
                'location_id' => $whitefield->id,
                'price' => 19500000,
                'built_up_area' => 2800,
                'plot_area' => 3200,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'furnishing_status' => 'unfurnished',
                'possession_status' => 'ready_to_move',
                'address' => 'ITPL Main Road, Whitefield',
                'contact_phone' => $owner->phone,
                'contact_email' => $owner->email,
                'featured' => false,
            ],
            [
                'user' => $agent,
                'title' => 'Fully Furnished Office Space in Koramangala',
                'listing_type' => 'lease',
                'property_category_id' => $officeCategory->id,
                'location_id' => $koramangala->id,
                'rent_amount' => 120000,
                'security_deposit' => 500000,
                'built_up_area' => 2200,
                'furnishing_status' => 'furnished',
                'possession_status' => 'ready_to_move',
                'address' => '80 Feet Road, Koramangala',
                'contact_phone' => $agent->phone,
                'contact_email' => $agent->email,
                'featured' => false,
            ],
        ];

        foreach ($listings as $data) {
            $owner = $data['user'];
            $featured = $data['featured'];
            unset($data['user'], $data['featured']);

            $property = $propertyService->create($owner, [...$data, 'amenity_ids' => $amenityIds]);
            $property = $propertyService->submitForReview($property);
            $property = $propertyService->approve($property);
            if ($featured) {
                $propertyService->toggleFeatured($property, true);
            }
        }

        if (PropertyRequirement::query()->count() === 0) {
            $requirementService->create($buyer, [
                'title' => 'Looking for a 2-3 BHK apartment to buy in Koramangala',
                'intent' => 'buy',
                'property_nature' => 'residential',
                'property_category_id' => $apartmentCategory->id,
                'city_id' => $bengaluru->id,
                'preferred_locality_ids' => [$koramangala->id],
                'budget_min' => 15000000,
                'budget_max' => 25000000,
                'area_min' => 900,
                'area_max' => 1400,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'furnishing_status' => 'semi_furnished',
                'contact_name' => $buyer->name,
                'contact_email' => $buyer->email,
                'contact_phone' => $buyer->phone,
                'preferred_contact_method' => 'phone',
            ]);
        }
    }
}
