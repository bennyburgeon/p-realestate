<?php

namespace App\Models;

use App\Traits\HasStatus;
use App\Traits\HasStatusHistory;
use Database\Factories\EnquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['property_id', 'user_id', 'status_id', 'name', 'email', 'phone', 'message', 'source'])]
class Enquiry extends Model
{
    /** @use HasFactory<EnquiryFactory> */
    use HasFactory, HasStatus, HasStatusHistory, HasUuids;

    /**
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<SiteVisit, $this>
     */
    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class)->latest('scheduled_at');
    }
}
