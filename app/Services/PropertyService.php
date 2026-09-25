<?php

namespace App\Services;

use App\Jobs\RecomputePropertyMatchesJob;
use App\Models\Property;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyService
{
    public function __construct(
        private readonly PropertyMatchingService $matchingService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $owner, array $data): Property
    {
        return DB::transaction(function () use ($owner, $data) {
            $amenityIds = $data['amenity_ids'] ?? [];
            unset($data['amenity_ids']);

            $property = Property::create([
                ...$data,
                'user_id' => $owner->id,
                'slug' => $this->uniqueSlug($data['title']),
                'status_id' => Status::PROPERTY_DRAFT,
            ]);

            if (! empty($amenityIds)) {
                $property->amenities()->sync($amenityIds);
            }

            return $property;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $amenityIds = $data['amenity_ids'] ?? null;
            unset($data['amenity_ids']);

            if (isset($data['title']) && $data['title'] !== $property->title) {
                $data['slug'] = $this->uniqueSlug($data['title'], $property->id);
            }

            $property->update($data);

            if ($amenityIds !== null) {
                $property->amenities()->sync($amenityIds);
            }

            return $property->refresh();
        });
    }

    public function submitForReview(Property $property): Property
    {
        $property->update(['status_id' => Status::PROPERTY_PENDING_REVIEW]);

        return $property;
    }

    public function approve(Property $property): Property
    {
        $property->update([
            'status_id' => Status::PROPERTY_AVAILABLE,
            'published_at' => $property->published_at ?? now(),
        ]);

        RecomputePropertyMatchesJob::dispatchSync($property);

        return $property;
    }

    public function reject(Property $property): Property
    {
        $property->update(['status_id' => Status::PROPERTY_REJECTED]);

        return $property;
    }

    public function transitionStatus(Property $property, int $statusId): Property
    {
        $property->update(['status_id' => $statusId]);

        if (in_array($statusId, Status::publicPropertyStatuses(), true)) {
            RecomputePropertyMatchesJob::dispatchSync($property);
        }

        return $property;
    }

    public function toggleFeatured(Property $property, bool $featured): Property
    {
        $property->update(['is_featured' => $featured]);

        return $property;
    }

    private function uniqueSlug(string $title, ?string $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (
            Property::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
