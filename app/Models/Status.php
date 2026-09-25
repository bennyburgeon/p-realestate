<?php

namespace App\Models;

use Database\Factories\StatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['group', 'slug', 'label', 'color', 'sort_order'])]
class Status extends Model
{
    /** @use HasFactory<StatusFactory> */
    use HasFactory;

    // Property lifecycle statuses (group: "property")
    public const int PROPERTY_DRAFT = 1;

    public const int PROPERTY_PENDING_REVIEW = 2;

    public const int PROPERTY_PUBLISHED = 3;

    public const int PROPERTY_AVAILABLE = 4;

    public const int PROPERTY_RESERVED = 5;

    public const int PROPERTY_UNDER_NEGOTIATION = 6;

    public const int PROPERTY_SOLD = 7;

    public const int PROPERTY_RENTED = 8;

    public const int PROPERTY_LEASED = 9;

    public const int PROPERTY_EXPIRED = 10;

    public const int PROPERTY_REJECTED = 11;

    public const int PROPERTY_SUSPENDED = 12;

    public const int PROPERTY_ARCHIVED = 13;

    // Property requirement statuses (group: "requirement")
    public const int REQUIREMENT_DRAFT = 14;

    public const int REQUIREMENT_ACTIVE = 15;

    public const int REQUIREMENT_PAUSED = 16;

    public const int REQUIREMENT_FULFILLED = 17;

    public const int REQUIREMENT_EXPIRED = 18;

    public const int REQUIREMENT_CLOSED = 19;

    // Requirement response statuses (group: "requirement_response")
    public const int RESPONSE_SENT = 20;

    public const int RESPONSE_VIEWED = 21;

    public const int RESPONSE_CONTACTED = 22;

    // Enquiry statuses (group: "enquiry")
    public const int ENQUIRY_NEW = 23;

    public const int ENQUIRY_CONTACTED = 24;

    public const int ENQUIRY_CLOSED = 25;

    /**
     * Statuses considered "live" on the public marketplace.
     *
     * @return array<int, int>
     */
    public static function publicPropertyStatuses(): array
    {
        return [
            self::PROPERTY_PUBLISHED,
            self::PROPERTY_AVAILABLE,
            self::PROPERTY_RESERVED,
            self::PROPERTY_UNDER_NEGOTIATION,
        ];
    }
}
