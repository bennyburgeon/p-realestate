<?php

namespace App\Services;

use App\Jobs\RecomputePropertyMatchesJob;
use App\Models\PropertyRequirement;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyRequirementService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, array $data, bool $submit = true): PropertyRequirement
    {
        return DB::transaction(function () use ($user, $data, $submit) {
            $requirement = PropertyRequirement::create([
                ...$data,
                'user_id' => $user->id,
                'reference_number' => $this->uniqueReferenceNumber(),
                'slug' => $this->uniqueSlug($data['title']),
                'status_id' => $submit ? Status::REQUIREMENT_ACTIVE : Status::REQUIREMENT_DRAFT,
                'expires_at' => $submit ? now()->addDays(30) : null,
            ]);

            if ($submit) {
                RecomputePropertyMatchesJob::dispatchSync($requirement);
            }

            return $requirement;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PropertyRequirement $requirement, array $data): PropertyRequirement
    {
        if (isset($data['title']) && $data['title'] !== $requirement->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $requirement->id);
        }

        $requirement->update($data);

        if ($requirement->status_id === Status::REQUIREMENT_ACTIVE) {
            RecomputePropertyMatchesJob::dispatchSync($requirement);
        }

        return $requirement->refresh();
    }

    public function submit(PropertyRequirement $requirement): PropertyRequirement
    {
        $requirement->update([
            'status_id' => Status::REQUIREMENT_ACTIVE,
            'expires_at' => now()->addDays(30),
        ]);

        RecomputePropertyMatchesJob::dispatchSync($requirement);

        return $requirement;
    }

    public function pause(PropertyRequirement $requirement): PropertyRequirement
    {
        $requirement->update(['status_id' => Status::REQUIREMENT_PAUSED]);

        return $requirement;
    }

    public function resume(PropertyRequirement $requirement): PropertyRequirement
    {
        $requirement->update([
            'status_id' => Status::REQUIREMENT_ACTIVE,
            'expires_at' => now()->addDays(30),
        ]);

        RecomputePropertyMatchesJob::dispatchSync($requirement);

        return $requirement;
    }

    public function close(PropertyRequirement $requirement): PropertyRequirement
    {
        $requirement->update(['status_id' => Status::REQUIREMENT_CLOSED]);

        return $requirement;
    }

    public function markFulfilled(PropertyRequirement $requirement): PropertyRequirement
    {
        $requirement->update([
            'status_id' => Status::REQUIREMENT_FULFILLED,
            'fulfilled_at' => now(),
        ]);

        return $requirement;
    }

    public function renew(PropertyRequirement $requirement): PropertyRequirement
    {
        $requirement->update([
            'status_id' => Status::REQUIREMENT_ACTIVE,
            'expires_at' => now()->addDays(30),
            'fulfilled_at' => null,
        ]);

        RecomputePropertyMatchesJob::dispatchSync($requirement);

        return $requirement;
    }

    private function uniqueReferenceNumber(): string
    {
        do {
            $reference = 'REQ-'.now()->format('Y').'-'.Str::upper(Str::random(6));
        } while (PropertyRequirement::query()->where('reference_number', $reference)->exists());

        return $reference;
    }

    private function uniqueSlug(string $title, ?string $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (
            PropertyRequirement::query()
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
