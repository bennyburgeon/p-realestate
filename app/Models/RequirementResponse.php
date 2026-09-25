<?php

namespace App\Models;

use App\Traits\HasStatus;
use Database\Factories\RequirementResponseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['property_requirement_id', 'responder_id', 'property_id', 'status_id', 'message'])]
class RequirementResponse extends Model
{
    /** @use HasFactory<RequirementResponseFactory> */
    use HasFactory, HasStatus, HasUuids;

    /**
     * @return BelongsTo<PropertyRequirement, $this>
     */
    public function requirement(): BelongsTo
    {
        return $this->belongsTo(PropertyRequirement::class, 'property_requirement_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    /**
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
