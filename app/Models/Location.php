<?php

namespace App\Models;

use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'parent_id', 'type', 'name', 'slug', 'state', 'district', 'country',
    'pin_code', 'latitude', 'longitude', 'is_popular',
])]
class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    public const string TYPE_CITY = 'city';

    public const string TYPE_LOCALITY = 'locality';

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_popular' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * @return HasMany<Location, $this>
     */
    public function localities(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * @return HasMany<Property, $this>
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function displayName(): string
    {
        return $this->type === self::TYPE_LOCALITY && $this->parent
            ? "{$this->name}, {$this->parent->name}"
            : $this->name;
    }

    /**
     * The administrative region around this location (e.g. "Ernakulam, Kerala"),
     * used to tell apart same-named places in pickers. Null when nothing is recorded.
     */
    public function regionLabel(): ?string
    {
        $district = $this->district ?? $this->parent?->district;
        $state = $this->state ?? $this->parent?->state;

        $parts = array_values(array_unique(array_filter([$district, $state])));

        return $parts === [] ? null : implode(', ', $parts);
    }

    /**
     * @param  Builder<Location>  $query
     * @return Builder<Location>
     */
    public function scopeCities(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CITY);
    }

    /**
     * Cities ordered for grouped pickers: districted cities first (by district), then the rest.
     *
     * @param  Builder<Location>  $query
     * @return Builder<Location>
     */
    public function scopeOrderedForPicker(Builder $query): Builder
    {
        return $query->orderByRaw('district is null')->orderBy('district')->orderBy('name');
    }

    /**
     * Popular cities ranked by how many live listings they hold (including listings
     * placed in their localities), so the homepage reflects real inventory instead of
     * an arbitrary or alphabetical order.
     *
     * @return Collection<int, Location>
     */
    public static function popularCitiesByLiveListings(int $limit = 6): Collection
    {
        $cities = self::query()->cities()->where('is_popular', true)->get();

        if ($cities->isEmpty()) {
            return $cities;
        }

        $cityIdByLocationId = self::query()
            ->whereIn('id', $cities->modelKeys())
            ->orWhereIn('parent_id', $cities->modelKeys())
            ->get(['id', 'parent_id', 'type'])
            ->mapWithKeys(fn (Location $location) => [
                $location->id => $location->type === self::TYPE_CITY ? $location->id : $location->parent_id,
            ]);

        $listingCounts = Property::query()
            ->live()
            ->whereIn('location_id', $cityIdByLocationId->keys())
            ->selectRaw('location_id, count(*) as aggregate')
            ->groupBy('location_id')
            ->pluck('aggregate', 'location_id')
            ->groupBy(fn ($count, $locationId) => $cityIdByLocationId[$locationId], preserveKeys: true)
            ->map(fn ($counts) => $counts->sum());

        return $cities
            ->each(fn (Location $city) => $city->setAttribute('properties_count', (int) ($listingCounts[$city->id] ?? 0)))
            ->sortBy([['properties_count', 'desc'], ['name', 'asc']])
            ->take($limit)
            ->values();
    }

    /**
     * Locations a buyer/tenant can pick as "preferred areas" once a city is chosen:
     * the city's own localities plus, when the city belongs to a district, the other
     * cities/local bodies of that district (Kerala local bodies have no localities).
     *
     * @return Collection<int, Location>
     */
    public function preferredAreaOptions(): Collection
    {
        $localities = $this->localities()->orderBy('name')->get();

        if (! $this->district) {
            return $localities;
        }

        $neighbours = self::query()
            ->cities()
            ->where('district', $this->district)
            ->whereKeyNot($this->getKey())
            ->orderBy('name')
            ->get();

        return $localities->concat($neighbours)->values();
    }
}
