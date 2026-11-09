<?php

use App\Models\Location;

beforeEach(function () {
    $this->kochi = Location::create(['type' => 'city', 'name' => 'Kochi', 'slug' => 'kochi-ernakulam', 'district' => 'Ernakulam', 'state' => 'Kerala']);
    $this->kakkanad = Location::create(['type' => 'locality', 'name' => 'Kakkanad', 'slug' => 'kakkanad', 'parent_id' => $this->kochi->id]);
    $this->kollam = Location::create(['type' => 'city', 'name' => 'Kollam', 'slug' => 'kollam-kollam', 'district' => 'Kollam', 'state' => 'Kerala']);
});

it('returns matching cities and localities in select2 format with their region', function () {
    $this->getJson(route('locations.options', ['q' => 'Ka']))
        ->assertOk()
        ->assertExactJson([
            'results' => [
                ['id' => $this->kakkanad->id, 'text' => 'Kakkanad, Kochi', 'meta' => 'Ernakulam, Kerala', 'type' => 'locality'],
            ],
            'pagination' => ['more' => false],
        ]);
});

it('limits results to the localities of the given parent city keyed by slug', function () {
    Location::create(['type' => 'locality', 'name' => 'Chinnakada', 'slug' => 'chinnakada', 'parent_id' => $this->kollam->id]);

    $this->getJson(route('locations.options', ['type' => 'locality', 'parent' => 'kochi-ernakulam', 'key' => 'slug']))
        ->assertOk()
        ->assertJsonPath('results.*.id', ['kakkanad']);
});

it('reports when more pages of options are available', function () {
    foreach (range(1, 21) as $number) {
        Location::create(['type' => 'city', 'name' => "Town {$number}", 'slug' => "town-{$number}"]);
    }

    $this->getJson(route('locations.options', ['type' => 'city', 'q' => 'Town']))
        ->assertOk()
        ->assertJsonCount(20, 'results')
        ->assertJsonPath('pagination.more', true);
});

it('rejects an unknown location type with 422', function () {
    $this->getJson(route('locations.options', ['type' => 'country']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('type');
});
