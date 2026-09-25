<?php

namespace App\Models;

use Database\Factories\PropertyMatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['property_requirement_id', 'property_id', 'score', 'match_band', 'is_hidden', 'computed_at'])]
class PropertyMatch extends Model
{
    /** @use HasFactory<PropertyMatchFactory> */
    use HasFactory, HasUuids;

    public const string BAND_EXCELLENT = 'excellent';

    public const string BAND_GOOD = 'good';

    public const string BAND_POSSIBLE = 'possible';

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_hidden' => 'boolean',
            'computed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<PropertyRequirement, $this>
     */
    public function requirement(): BelongsTo
    {
        return $this->belongsTo(PropertyRequirement::class, 'property_requirement_id');
    }

    /**
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function label(): string
    {
        return match ($this->match_band) {
            self::BAND_EXCELLENT => 'Excellent Match',
            self::BAND_GOOD => 'Good Match',
            default => 'Possible Match',
        };
    }
}
