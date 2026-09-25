<?php

namespace App\Models;

use Database\Factories\PropertyCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['parent_id', 'name', 'slug', 'nature', 'icon', 'sort_order'])]
class PropertyCategory extends Model
{
    /** @use HasFactory<PropertyCategoryFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    public const string NATURE_RESIDENTIAL = 'residential';

    public const string NATURE_COMMERCIAL = 'commercial';

    /**
     * @return BelongsTo<PropertyCategory, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class, 'parent_id');
    }

    /**
     * @return HasMany<PropertyCategory, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(PropertyCategory::class, 'parent_id');
    }

    /**
     * @return HasMany<Property, $this>
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
