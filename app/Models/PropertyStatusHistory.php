<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subject_type', 'subject_id', 'user_id', 'from_status_id', 'to_status_id', 'reason', 'notes'])]
class PropertyStatusHistory extends Model
{
    use HasUuids;

    const UPDATED_AT = null;

    /**
     * @return BelongsTo<Status, $this>
     */
    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'to_status_id');
    }

    /**
     * @return BelongsTo<Status, $this>
     */
    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'from_status_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
