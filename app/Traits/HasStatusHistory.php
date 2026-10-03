<?php

namespace App\Traits;

use App\Models\PropertyStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasStatusHistory
{
    /**
     * @return MorphMany<PropertyStatusHistory, $this>
     */
    public function statusHistories(): MorphMany
    {
        // created_at only has second-level precision and several transitions
        // can legitimately happen within the same second (e.g. in tests or
        // rapid admin actions), so order by the time-ordered UUID primary
        // key as a deterministic tiebreaker.
        return $this->morphMany(PropertyStatusHistory::class, 'subject')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function recordStatusChange(
        ?int $toStatusId,
        ?int $fromStatusId,
        ?User $actor,
        ?string $reason = null,
        ?string $notes = null,
    ): PropertyStatusHistory {
        return $this->statusHistories()->create([
            'user_id' => $actor?->id,
            'from_status_id' => $fromStatusId,
            'to_status_id' => $toStatusId,
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }
}
