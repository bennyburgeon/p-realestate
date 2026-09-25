<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\PropertyRequirement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class PropertyMatchingService
{
    /**
     * Minimum score (out of 100) for a match to be worth surfacing.
     */
    private const int MIN_SCORE = 30;

    /**
     * Recompute and persist matches for a single requirement against all live properties.
     *
     * @return Collection<int, PropertyMatch>
     */
    public function findMatchesForRequirement(PropertyRequirement $requirement): Collection
    {
        $candidates = Property::query()
            ->live()
            ->with(['location', 'category'])
            ->get();

        return $this->syncMatches($requirement, $candidates);
    }

    /**
     * Recompute and persist matches for a single property against all active requirements.
     *
     * @return Collection<int, PropertyMatch>
     */
    public function findMatchingRequirementsForProperty(Property $property): Collection
    {
        $requirements = PropertyRequirement::query()
            ->active()
            ->with(['city', 'category'])
            ->get();

        $results = new Collection;

        foreach ($requirements as $requirement) {
            $match = $this->upsertMatch($requirement, $property);
            if ($match) {
                $results->push($match);
            } else {
                PropertyMatch::query()
                    ->where('property_requirement_id', $requirement->id)
                    ->where('property_id', $property->id)
                    ->delete();
            }
        }

        return $results;
    }

    /**
     * @param  Collection<int, Property>  $candidates
     * @return Collection<int, PropertyMatch>
     */
    private function syncMatches(PropertyRequirement $requirement, Collection $candidates): Collection
    {
        $results = new Collection;
        $keepPropertyIds = [];

        foreach ($candidates as $property) {
            $match = $this->upsertMatch($requirement, $property);
            if ($match) {
                $results->push($match);
                $keepPropertyIds[] = $property->id;
            }
        }

        PropertyMatch::query()
            ->where('property_requirement_id', $requirement->id)
            ->whereNotIn('property_id', $keepPropertyIds)
            ->delete();

        return $results;
    }

    private function upsertMatch(PropertyRequirement $requirement, Property $property): ?PropertyMatch
    {
        $score = $this->score($requirement, $property);

        if ($score === null || $score < self::MIN_SCORE) {
            return null;
        }

        return PropertyMatch::updateOrCreate(
            [
                'property_requirement_id' => $requirement->id,
                'property_id' => $property->id,
            ],
            [
                'score' => $score,
                'match_band' => $this->band($score),
                'computed_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Score a property against a requirement, 0-100. Returns null on a hard mismatch
     * (wrong intent or property nature) that should never be surfaced as a match.
     */
    private function score(PropertyRequirement $requirement, Property $property): ?float
    {
        if (! $this->intentMatches($requirement->intent, $property->listing_type)) {
            return null;
        }

        if ($requirement->property_category_id && $property->category
            && $requirement->property_category_id !== $property->property_category_id
            && $requirement->property_nature !== $property->category->nature) {
            return null;
        }

        $score = 0.0;

        // Category (25)
        if ($requirement->property_category_id && $requirement->property_category_id === $property->property_category_id) {
            $score += 25;
        } elseif ($property->category && $property->category->nature === $requirement->property_nature) {
            $score += 10;
        }

        // Location (25)
        $score += $this->locationScore($requirement, $property);

        // Budget (20)
        $score += $this->rangeScore(
            $requirement->budget_min,
            $requirement->budget_max,
            (float) ($property->listing_type === Property::LISTING_SALE ? $property->price : $property->rent_amount),
            weight: 20,
        );

        // Area (15)
        $score += $this->rangeScore(
            $requirement->area_min,
            $requirement->area_max,
            (float) ($property->built_up_area ?? $property->carpet_area ?? 0),
            weight: 15,
        );

        // Bedrooms (10)
        if ($requirement->bedrooms !== null && $property->bedrooms !== null) {
            $diff = abs($requirement->bedrooms - $property->bedrooms);
            $score += match (true) {
                $diff === 0 => 10,
                $diff === 1 => 5,
                default => 0,
            };
        } elseif ($requirement->bedrooms === null) {
            $score += 5;
        }

        // Furnishing (5)
        if ($requirement->furnishing_status && $requirement->furnishing_status === $property->furnishing_status) {
            $score += 5;
        } elseif (! $requirement->furnishing_status) {
            $score += 2.5;
        }

        return round(min($score, 100), 2);
    }

    private function intentMatches(string $intent, string $listingType): bool
    {
        return match ($intent) {
            PropertyRequirement::INTENT_BUY => $listingType === Property::LISTING_SALE,
            PropertyRequirement::INTENT_RENT => $listingType === Property::LISTING_RENT,
            PropertyRequirement::INTENT_LEASE => $listingType === Property::LISTING_LEASE,
            default => false,
        };
    }

    private function locationScore(PropertyRequirement $requirement, Property $property): float
    {
        if (! $requirement->city_id || ! $property->location) {
            return 5;
        }

        $propertyCityId = $property->location->type === Location::TYPE_CITY
            ? $property->location->id
            : $property->location->parent_id;

        if ($propertyCityId !== $requirement->city_id) {
            return 0;
        }

        $preferredLocalities = $requirement->preferred_locality_ids ?? [];
        if (empty($preferredLocalities)) {
            return 15;
        }

        return in_array($property->location_id, $preferredLocalities, true) ? 25 : 12;
    }

    private function rangeScore(?string $min, ?string $max, float $value, int $weight): float
    {
        if (! $min && ! $max) {
            return $weight * 0.5;
        }

        if ($value <= 0) {
            return 0;
        }

        $min = $min !== null ? (float) $min : null;
        $max = $max !== null ? (float) $max : null;

        if (($min === null || $value >= $min) && ($max === null || $value <= $max)) {
            return $weight;
        }

        // Within 15% tolerance outside the requested range = partial credit.
        $tolerance = 0.15;
        $nearMin = $min !== null && $value >= $min * (1 - $tolerance);
        $nearMax = $max !== null && $value <= $max * (1 + $tolerance);

        if (($min === null || $nearMin) && ($max === null || $nearMax)) {
            return $weight * 0.5;
        }

        return 0;
    }

    private function band(float $score): string
    {
        return match (true) {
            $score >= 75 => PropertyMatch::BAND_EXCELLENT,
            $score >= 50 => PropertyMatch::BAND_GOOD,
            default => PropertyMatch::BAND_POSSIBLE,
        };
    }
}
