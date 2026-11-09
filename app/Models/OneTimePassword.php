<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

/**
 * A single-use, hashed login code. A row is usable only while it is neither consumed
 * nor invalidated and has not expired.
 */
#[Fillable(['user_id', 'phone', 'code_hash', 'attempts', 'expires_at', 'consumed_at', 'invalidated_at', 'ip_address'])]
#[Hidden(['code_hash'])]
class OneTimePassword extends Model
{
    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'invalidated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Codes that have been neither used nor invalidated (they may still be expired).
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('consumed_at')->whereNull('invalidated_at');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function matches(string $code): bool
    {
        return Hash::check($code, $this->code_hash);
    }
}
