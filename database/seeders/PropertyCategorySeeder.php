<?php

namespace Database\Seeders;

use App\Models\PropertyCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertyCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'residential' => [
                'Apartment / Flat', 'Independent House / Villa', 'Independent Floor',
                'Plot / Land', 'Farmhouse', 'PG / Hostel / Shared Accommodation',
            ],
            'commercial' => [
                'Office Space', 'Shop / Showroom', 'Commercial Land',
                'Warehouse / Godown', 'Industrial Shed', 'Co-working Space',
            ],
        ];

        $order = 0;
        foreach ($categories as $nature => $names) {
            foreach ($names as $name) {
                PropertyCategory::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'nature' => $nature, 'sort_order' => $order++]
                );
            }
        }
    }
}
