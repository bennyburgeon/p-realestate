<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Mumbai' => ['Maharashtra', ['Andheri West', 'Bandra West', 'Powai', 'Thane West', 'Navi Mumbai']],
            'Bengaluru' => ['Karnataka', ['Whitefield', 'Koramangala', 'Indiranagar', 'Electronic City', 'HSR Layout']],
            'Delhi' => ['Delhi', ['Dwarka', 'Rohini', 'Saket', 'Vasant Kunj', 'Connaught Place']],
            'Pune' => ['Maharashtra', ['Hinjewadi', 'Kothrud', 'Baner', 'Viman Nagar', 'Wakad']],
            'Hyderabad' => ['Telangana', ['Gachibowli', 'Madhapur', 'Kondapur', 'Banjara Hills', 'Kukatpally']],
        ];

        $popularCitySlugs = ['mumbai', 'bengaluru', 'delhi'];

        foreach ($data as $city => [$state, $localities]) {
            $cityModel = Location::firstOrCreate(
                ['slug' => Str::slug($city), 'parent_id' => null],
                [
                    'type' => Location::TYPE_CITY,
                    'name' => $city,
                    'state' => $state,
                    'country' => 'India',
                    'is_popular' => in_array(Str::slug($city), $popularCitySlugs, true),
                ]
            );

            foreach ($localities as $locality) {
                Location::firstOrCreate(
                    ['slug' => Str::slug($locality), 'parent_id' => $cityModel->id],
                    [
                        'type' => Location::TYPE_LOCALITY,
                        'name' => $locality,
                        'state' => $state,
                        'country' => 'India',
                    ]
                );
            }
        }
    }
}
