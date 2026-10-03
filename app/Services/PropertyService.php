<?php

namespace App\Services;

use App\Jobs\RecomputePropertyMatchesJob;
use App\Models\Location;
use App\Models\Property;
use App\Models\Status;
use App\Models\User;
use App\Notifications\PropertyApproved;
use App\Notifications\PropertyChangesRequested;
use App\Notifications\PropertyMarkedSold;
use App\Notifications\PropertyRejected;
use App\Notifications\PropertySubmittedForReview;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
                'slug' => $this->uniqueSlug($data['title'], null, $data['listing_type'] ?? null, $data['location_id'] ?? null),
                'status_id' => Status::PROPERTY_DRAFT,
            ]);

            if (! empty($amenityIds)) {
                $property->amenities()->sync($amenityIds);
            }

            $property->recordStatusChange(Status::PROPERTY_DRAFT, null, $owner);

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
                $data['slug'] = $this->uniqueSlug(
                    $data['title'],
                    $property->id,
                    $data['listing_type'] ?? $property->listing_type,
                    $data['location_id'] ?? $property->location_id,
                );
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
        $from = $property->status_id;
        $property->update(['status_id' => Status::PROPERTY_PENDING_REVIEW]);
        $property->recordStatusChange(Status::PROPERTY_PENDING_REVIEW, $from, $property->owner);

        Notification::send($this->adminRecipients(), new PropertySubmittedForReview($property));

        return $property;
    }

    public function approve(Property $property, ?User $actor = null): Property
    {
        $from = $property->status_id;

        $property->update([
            'status_id' => Status::PROPERTY_AVAILABLE,
            'published_at' => $property->published_at ?? now(),
        ]);

        $property->recordStatusChange(Status::PROPERTY_AVAILABLE, $from, $actor);

        RecomputePropertyMatchesJob::dispatchSync($property);

        $property->owner->notify(new PropertyApproved($property));

        return $property;
    }

    public function reject(Property $property, ?User $actor = null, ?string $reason = null, ?string $notes = null): Property
    {
        $from = $property->status_id;
        $property->update(['status_id' => Status::PROPERTY_REJECTED]);
        $property->recordStatusChange(Status::PROPERTY_REJECTED, $from, $actor, $reason, $notes);

        $property->owner->notify(new PropertyRejected($property, $reason));

        return $property;
    }

    public function requestChanges(Property $property, ?User $actor, string $reason, ?string $notes = null): Property
    {
        $from = $property->status_id;
        $property->update(['status_id' => Status::PROPERTY_CHANGES_REQUESTED]);
        $property->recordStatusChange(Status::PROPERTY_CHANGES_REQUESTED, $from, $actor, $reason, $notes);

        $property->owner->notify(new PropertyChangesRequested($property, $reason));

        return $property;
    }

    public function transitionStatus(Property $property, int $statusId, ?User $actor = null, ?string $reason = null): Property
    {
        $from = $property->status_id;
        $property->update(['status_id' => $statusId]);
        $property->recordStatusChange($statusId, $from, $actor, $reason);

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

    /**
     * @param  array{sold_at?: ?string, sold_price?: ?float, buyer_source?: ?string, sold_notes?: ?string}  $data
     */
    public function markAsSold(Property $property, User $actor, array $data): Property
    {
        $from = $property->status_id;

        $soldStatus = match ($property->listing_type) {
            Property::LISTING_RENT => Status::PROPERTY_RENTED,
            Property::LISTING_LEASE => Status::PROPERTY_LEASED,
            default => Status::PROPERTY_SOLD,
        };

        $property->update([
            'status_id' => $soldStatus,
            'sold_at' => $data['sold_at'] ?? now(),
            'sold_price' => $data['sold_price'] ?? null,
            'buyer_source' => $data['buyer_source'] ?? null,
            'sold_notes' => $data['sold_notes'] ?? null,
        ]);

        $property->recordStatusChange($soldStatus, $from, $actor);

        $property->owner->notify(new PropertyMarkedSold($property));

        return $property;
    }

    /**
     * @return Collection<int, User>
     */
    private function adminRecipients(): Collection
    {
        return User::role(['admin', 'super_admin'])->get();
    }

    private function uniqueSlug(string $title, ?string $ignoreId = null, ?string $listingType = null, ?string $locationId = null): string
    {
        $location = $locationId ? Location::find($locationId) : null;

        $parts = array_filter([
            Str::slug($title),
            $listingType ? 'for-'.$listingType : null,
            $location?->slug,
            $location?->parent?->slug,
        ]);

        $base = implode('-', $parts);
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
