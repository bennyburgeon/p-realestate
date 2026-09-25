<?php

namespace App\Models;

use App\Traits\HasStatus;
use Database\Factories\PropertyRequirementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id', 'property_category_id', 'city_id', 'status_id',
    'reference_number', 'title', 'slug', 'intent', 'property_nature',
    'preferred_locality_ids', 'budget_min', 'budget_max', 'area_min', 'area_max', 'area_unit',
    'bedrooms', 'bathrooms', 'furnishing_status', 'parking_required',
    'floor_preference', 'facing_preference', 'property_age', 'possession_requirement',
    'move_in_date', 'amenity_ids', 'special_requirements', 'additional_notes',
    'contact_name', 'contact_email', 'contact_phone', 'preferred_contact_method', 'preferred_contact_time',
    'expires_at', 'fulfilled_at',
])]
class PropertyRequirement extends Model
{
    /** @use HasFactory<PropertyRequirementFactory> */
    use HasFactory, HasStatus, HasUuids, SoftDeletes;

    public const string INTENT_BUY = 'buy';

    public const string INTENT_RENT = 'rent';

    public const string INTENT_LEASE = 'lease';

    protected function casts(): array
    {
        return [
            'preferred_locality_ids' => 'array',
            'amenity_ids' => 'array',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'area_min' => 'decimal:2',
            'area_max' => 'decimal:2',
            'parking_required' => 'boolean',
            'move_in_date' => 'date',
            'expires_at' => 'datetime',
            'fulfilled_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<PropertyCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class, 'property_category_id');
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'city_id');
    }

    /**
     * @return HasMany<RequirementResponse, $this>
     */
    public function responses(): HasMany
    {
        return $this->hasMany(RequirementResponse::class);
    }

    /**
     * @return HasMany<PropertyMatch, $this>
     */
    public function matches(): HasMany
    {
        return $this->hasMany(PropertyMatch::class);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status_id', Status::REQUIREMENT_ACTIVE);
    }

    public function isEditable(): bool
    {
        return in_array($this->status_id, [
            Status::REQUIREMENT_DRAFT,
            Status::REQUIREMENT_ACTIVE,
            Status::REQUIREMENT_PAUSED,
        ], true);
    }
}
