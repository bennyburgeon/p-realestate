<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            'convenience' => ['Lift / Elevator', 'Power Backup', 'Parking', 'Water Supply 24x7', 'Internet / Wi-Fi'],
            'safety' => ['CCTV Surveillance', 'Security Guard', 'Gated Community', 'Fire Safety'],
            'lifestyle' => ['Swimming Pool', 'Gymnasium', 'Clubhouse', 'Children\'s Play Area', 'Garden / Park'],
            'nearby' => ['Near School', 'Near Hospital', 'Near Market', 'Near Metro / Transit'],
        ];

        $order = 0;
        foreach ($amenities as $category => $names) {
            foreach ($names as $name) {
                Amenity::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'category' => $category, 'sort_order' => $order++]
                );
            }
        }
    }
}
