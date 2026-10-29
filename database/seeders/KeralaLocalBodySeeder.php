<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class KeralaLocalBodySeeder extends Seeder
{
    /**
     * Matches each `local_bodies` row in database/kerala_local_bodies.sql, e.g.:
     * ('Kochi','Corporation',(SELECT district_id FROM districts WHERE name='Ernakulam'),94.88,677381,74,1967)
     */
    private const string LOCAL_BODY_PATTERN = <<<'REGEX'
        /\('((?:[^'\\]|\\.)*)','(Corporation|Municipality|Panchayat)',\(SELECT district_id FROM districts WHERE name='([^']+)'\),[^,]+,[^,]+,[^,]+,[^)]+\)/
        REGEX;

    public function run(): void
    {
        $path = database_path('kerala_local_bodies.sql');

        if (! is_file($path)) {
            throw new RuntimeException("Kerala local bodies source file not found: {$path}");
        }

        preg_match_all(self::LOCAL_BODY_PATTERN, file_get_contents($path), $matches, PREG_SET_ORDER);

        if ($matches === []) {
            throw new RuntimeException('No local bodies could be parsed from kerala_local_bodies.sql.');
        }

        foreach ($matches as $row) {
            [, $name, $bodyType, $district] = $row;

            $name = stripslashes($name);

            Location::updateOrCreate(
                [
                    'type' => Location::TYPE_CITY,
                    'name' => $name,
                    'district' => $district,
                    'parent_id' => null,
                ],
                [
                    'slug' => Str::slug("{$name}-{$district}"),
                    'state' => 'Kerala',
                    'country' => 'India',
                    'is_popular' => $bodyType === 'Corporation',
                ]
            );
        }
    }
}
