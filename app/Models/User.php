<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, HasStatus, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'status_id' => 'integer',
        ];
    }

    /**
     * True for an account created at the phone-number step whose OTP has not been
     * verified yet. Suspended accounts never qualify, so OTP login cannot unsuspend.
     */
    public function isAwaitingPhoneVerification(): bool
    {
        return $this->status_id === Status::USER_INACTIVE && $this->is_active;
    }

    /**
     * @return HasMany<OneTimePassword, $this>
     */
    public function oneTimePasswords(): HasMany
    {
        return $this->hasMany(OneTimePassword::class);
    }

    /**
     * @return HasMany<Property, $this>
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * @return HasMany<PropertyRequirement, $this>
     */
    public function propertyRequirements(): HasMany
    {
        return $this->hasMany(PropertyRequirement::class);
    }

    /**
     * @return HasMany<Favourite, $this>
     */
    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * @return HasMany<Enquiry, $this>
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    /**
     * @return HasMany<RequirementResponse, $this>
     */
    public function requirementResponses(): HasMany
    {
        return $this->hasMany(RequirementResponse::class, 'responder_id');
    }
}
