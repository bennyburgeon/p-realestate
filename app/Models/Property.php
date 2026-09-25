<?php

namespace App\Models;

use App\Traits\HasStatus;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'user_id', 'property_category_id', 'location_id', 'status_id',
    'title', 'slug', 'listing_type', 'description',
    'price', 'rent_amount', 'security_deposit', 'maintenance_amount', 'is_negotiable',
    'built_up_area', 'carpet_area', 'plot_area', 'area_unit',
    'bedrooms', 'bathrooms', 'balconies', 'floor_number', 'total_floors',
    'furnishing_status', 'parking_details', 'facing', 'construction_year',
    'possession_status', 'availability_date',
    'address', 'landmark', 'pin_code', 'latitude', 'longitude',
    'contact_preference', 'contact_phone', 'contact_email',
    'is_verified', 'is_featured', 'published_at',
])]
class Property extends Model implements HasMedia
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, HasStatus, HasUuids, InteractsWithMedia, SoftDeletes;

    public const string LISTING_SALE = 'sale';

    public const string LISTING_RENT = 'rent';

    public const string LISTING_LEASE = 'lease';

    public const string FURNISHING_UNFURNISHED = 'unfurnished';

    public const string FURNISHING_SEMI_FURNISHED = 'semi_furnished';

    public const string FURNISHING_FURNISHED = 'furnished';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'rent_amount' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'maintenance_amount' => 'decimal:2',
            'is_negotiable' => 'boolean',
            'built_up_area' => 'decimal:2',
            'carpet_area' => 'decimal:2',
            'plot_area' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'availability_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/property-placeholder.svg');

        $this->addMediaCollection('documents');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)->height(300)->performOnCollections('images');

        $this->addMediaConversion('large')
            ->width(1600)->height(1200)->performOnCollections('images');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return BelongsToMany<Amenity, $this>
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities');
    }

    /**
     * @return HasMany<Enquiry, $this>
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    /**
     * @return HasMany<Favourite, $this>
     */
    public function favouritedBy(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * @return HasMany<PropertyMatch, $this>
     */
    public function matches(): HasMany
    {
        return $this->hasMany(PropertyMatch::class);
    }

    /**
     * @return HasMany<RequirementResponse, $this>
     */
    public function requirementResponses(): HasMany
    {
        return $this->hasMany(RequirementResponse::class);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeLive(Builder $query): Builder
    {
        return $query->whereIn('status_id', Status::publicPropertyStatuses());
    }

    /**
     * @param  Builder<$this>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<$this>
     */
    public function scopeFilterFromRequest(Builder $query, array $filters): Builder
    {
        $intentMap = [
            'buy' => self::LISTING_SALE,
            'rent' => self::LISTING_RENT,
            'lease' => self::LISTING_LEASE,
        ];

        return $query
            ->when(
                ! empty($filters['intent']) && isset($intentMap[$filters['intent']]),
                fn ($q) => $q->where('listing_type', $intentMap[$filters['intent']])
            )
            ->when(! empty($filters['nature']), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('nature', $filters['nature'])))
            ->when(! empty($filters['category']), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $filters['category'])))
            ->when(! empty($filters['city']), function ($q) use ($filters) {
                $q->whereHas('location', function ($l) use ($filters) {
                    $l->where('slug', $filters['city'])->orWhereHas('parent', fn ($p) => $p->where('slug', $filters['city']));
                });
            })
            ->when(! empty($filters['locality']), fn ($q) => $q->whereHas('location', fn ($l) => $l->where('slug', $filters['locality'])))
            ->when(! empty($filters['keyword']), function ($q) use ($filters) {
                $keyword = $filters['keyword'];
                $q->where(function ($w) use ($keyword) {
                    $w->where('title', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%")
                        ->orWhere('landmark', 'like', "%{$keyword}%");
                });
            })
            ->when(! empty($filters['price_min']) || ! empty($filters['price_max']), function ($q) use ($filters) {
                $q->where(function ($w) use ($filters) {
                    foreach ([self::LISTING_SALE => 'price', self::LISTING_RENT => 'rent_amount', self::LISTING_LEASE => 'rent_amount'] as $listingType => $column) {
                        $w->orWhere(function ($inner) use ($listingType, $column, $filters) {
                            $inner->where('listing_type', $listingType)
                                ->when(! empty($filters['price_min']), fn ($x) => $x->where($column, '>=', $filters['price_min']))
                                ->when(! empty($filters['price_max']), fn ($x) => $x->where($column, '<=', $filters['price_max']));
                        });
                    }
                });
            })
            ->when(! empty($filters['area_min']), fn ($q) => $q->where('built_up_area', '>=', $filters['area_min']))
            ->when(! empty($filters['area_max']), fn ($q) => $q->where('built_up_area', '<=', $filters['area_max']))
            ->when(! empty($filters['bedrooms']), fn ($q) => $q->where('bedrooms', '>=', $filters['bedrooms']))
            ->when(! empty($filters['bathrooms']), fn ($q) => $q->where('bathrooms', '>=', $filters['bathrooms']))
            ->when(! empty($filters['furnishing']), fn ($q) => $q->where('furnishing_status', $filters['furnishing']))
            ->when(! empty($filters['verified_only']), fn ($q) => $q->where('is_verified', true))
            ->when(! empty($filters['featured_only']), fn ($q) => $q->where('is_featured', true));
    }

    public function displayPrice(): ?string
    {
        return match ($this->listing_type) {
            self::LISTING_SALE => $this->price,
            default => $this->rent_amount,
        };
    }
}
